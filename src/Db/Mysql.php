<?php

namespace Miaoxing\Plugin\Db;

use Miaoxing\Plugin\Service\QueryBuilder;

class Mysql extends BaseDriver
{
    // The query types.

    const SELECT = 0;
    const DELETE = 1;
    const UPDATE = 2;

    protected $wrapper = '`';

    protected $sqlParts = [];

    public function getSql($type, $sqlParts, $identifierConverter = null)
    {
        $this->aliases = [];
        $this->sqlParts = $sqlParts;
        $this->identifierConverter = $identifierConverter;

        switch ($type) {
            case self::DELETE:
                return $this->getSqlForDelete();

            case self::UPDATE:
                return $this->getSqlForUpdate();

            case self::SELECT:
            default:
                return $this->getSqlForSelect();
        }
    }

    /**
     * Returns the interpolated query.
     *
     * @param int $type
     * @param array $sqlParts
     * @param callable $identifierConverter
     * @param array $values
     * @return string
     * @link https://stackoverflow.com/a/8403150
     */
    public function getRawSql($type, $sqlParts, $identifierConverter, array $values)
    {
        $query = $this->getSql($type, $sqlParts, $identifierConverter);
        $keys = [];

        // build a regular expression for each parameter
        foreach ($values as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:' . $key . '/';
            } else {
                $keys[] = '/[?]/';
            }

            if (is_string($value)) {
                $values[$key] = "'" . $value . "'";
            } elseif (is_array($value)) {
                $values[$key] = "'" . implode("','", $value) . "'";
            } elseif (null === $value) {
                $values[$key] = 'NULL';
            }
        }

        return preg_replace($keys, $values, $query, 1);
    }

    /**
     * Converts this instance into an SELECT string in SQL
     *
     * @return string
     */
    protected function getSqlForSelect()
    {
        $parts = $this->sqlParts;

        if (!$parts['select']) {
            $parts['select'] = ['*'];
        }

        $query = 'SELECT ';

        if (isset($parts['distinct']) && $parts['distinct']) {
            $query .= 'DISTINCT ';
        }

        if ($parts['aggregate']) {
            $query .= $parts['aggregate']['function'] . '(' . $this->wrap($parts['aggregate']['columns']) . ')';
        } else {
            $selects = [];
            foreach ($parts['select'] as $as => $select) {
                if ($this->isRaw($select)) {
                    $selects[] = $this->getRawValue($select);
                } elseif (is_string($as)) {
                    $selects[] = $this->wrap($as) . ' AS ' . $this->wrap($select);
                } else {
                    $selects[] = $this->wrap($select);
                }
            }
            $query .= implode(', ', $selects);
        }

        $query .= ' FROM ' . $this->getFrom();

        // JOIN
        foreach ($parts['join'] as $join) {
            $query .= ' ' . strtoupper($join['type'])
                . ' JOIN ' . $this->parseTable($join['table'])
                . ' ON ' . $this->wrap($join['first']) . ' ' . $join['operator'] . ' ' . $this->wrap($join['second']);
        }

        if ($parts['where']) {
            $query .= ' WHERE ' . $this->buildWhere($parts);
        }

        if ($parts['groupBy']) {
            $query .= ' GROUP BY ';
            $groupBys = [];
            foreach ($parts['groupBy'] as $groupBy) {
                $groupBys[] = $this->wrap($groupBy);
            }
            $query .= implode(', ', $groupBys);
        }

        if ($parts['having']) {
            $query .= ' HAVING ' . $this->buildWhere($parts, 'having');
        }

        if (!$parts['aggregate']) {
            if ($parts['orderBy']) {
                $query .= ' ORDER BY ';
                $orderBys = [];
                foreach ($parts['orderBy'] as $orderBy) {
                    $orderBys[] = $this->wrap($orderBy['column']) . ' ' . $orderBy['order'];
                }
                $query .= implode(', ', $orderBys);
            }

            $query .= (null !== $parts['limit'] ? ' LIMIT ' . $parts['limit'] : '')
                . (null !== $parts['offset'] ? ' OFFSET ' . $parts['offset'] : '');
        }

        $query .= $this->generateLockSql();

        return $query;
    }

    protected function buildWhere(array $parts, string $type = 'where')
    {
        $wheres = $parts[$type];
        $defaultTable = $parts['join'] ? $this->parseTableAndAlias($parts['from'])[0] : null;

        $query = '';
        foreach ($wheres as $i => $where) {
            if (0 !== $i) {
                $query .= ' ' . $where['condition'] . ' ';
            }

            if ($this->isRaw($where['column'])) {
                $query .= $this->getRawValue($where['column']);
                continue;
            }

            if ($where['column'] instanceof QueryBuilder) {
                $sqlParts = $where['column']->getSqlParts();
                $query .= '(' . $this->buildWhere($sqlParts) . ')';
                continue;
            }

            $column = $this->wrap($where['column'], $defaultTable);
            switch ($where['type'] ?? '') {
                case 'DATE':
                case 'MONTH':
                case 'DAY':
                case 'YEAR':
                case 'TIME':
                    $column = $where['type'] . '(' . $column . ')';
                    break;

                case 'COLUMN':
                    $query .= $column . ' ' . $where['operator'] . ' ' . $this->wrap($where['value']);
                    // TODO refactor
                    continue 2;

                default:
                    break;
            }

            switch ($where['operator']) {
                case 'BETWEEN':
                case 'NOT BETWEEN':
                    $query .= $this->processCondition($column . ' ' . $where['operator'] . ' ? AND ?');
                    break;

                case 'IN':
                case 'NOT IN':
                    $placeholder = implode(', ', array_pad([], count($where['value']), '?'));
                    $query .= $this->processCondition($column . ' ' . $where['operator'] . ' (' . $placeholder . ')');
                    break;

                case 'NULL':
                case 'NOT NULL':
                    $query .= $this->processCondition($column . ' IS ' . $where['operator']);
                    break;

                default:
                    $query .= $this->processCondition($column . ' ' . ($where['operator'] ?: '=') . ' ?');
            }
        }

        return $query;
    }

    /**
     * Converts this instance into an SELECT COUNT string in SQL
     */
    protected function getSqlForCount()
    {
        return 'SELECT COUNT(*) FROM (' . $this->getSqlForSelect() . ') wei_count';
    }

    /**
     * Converts this instance into an UPDATE string in SQL.
     *
     * @return string
     */
    protected function getSqlForUpdate()
    {
        $query = 'UPDATE ' . $this->getFrom() . ' SET ';

        $sets = [];
        foreach ($this->sqlParts['set'] as $set) {
            $sets[] = $this->wrap($set) . ' = ?';
        }
        $query .= implode(', ', $sets);

        if ($this->sqlParts['where']) {
            $query .= ' WHERE ' . $this->buildWhere($this->sqlParts);
        }

        return $query;
    }

    /**
     * Converts this instance into a DELETE string in SQL.
     *
     * @return string
     */
    protected function getSqlForDelete()
    {
        return 'DELETE FROM ' . $this->getFrom()
            . ($this->sqlParts['where'] ? ' WHERE ' . $this->buildWhere($this->sqlParts) : '');
    }

    /**
     * Returns the from SQL part
     *
     * @return string
     */
    protected function getFrom()
    {
        return $this->parseTable($this->sqlParts['from']);
    }

    protected function parseTable($table)
    {
        [$table, $alias] = $this->parseTableAndAlias($table);
        return $this->wrapTable($table) . ($alias ? (' ' . $this->wrap($alias)) : '');
    }

    /**
     * Generate condition string for WHERE or Having statement
     *
     * @param mixed $conditions
     * @return string
     */
    protected function processCondition($conditions)
    {
        if (is_array($conditions)) {
            $where = [];
            $params = [];
            foreach ($conditions as $field => $condition) {
                if (is_array($condition)) {
                    $where[] = $field . ' IN (' . implode(', ', array_pad([], count($condition), '?')) . ')';
                    $params = array_merge($params, $condition);
                } else {
                    $where[] = $field . ' = ?';
                    $params[] = $condition;
                }
            }
            $conditions = implode(' AND ', $where);
        }

        return $conditions;
    }

    /**
     * @return string
     */
    protected function generateLockSql()
    {
        if ('' === $this->sqlParts['lock']) {
            return '';
        }

        if (is_string($this->sqlParts['lock'])) {
            return ' ' . $this->sqlParts['lock'];
        }

        if ($this->sqlParts['lock']) {
            return ' FOR UPDATE';
        } else {
            return ' LOCK IN SHARE MODE';
        }
    }

    private function parseTableAndAlias(string $table): array
    {
        $pos = strpos($table, ' ');
        if (false !== $pos) {
            [$table, $alias] = explode(' ', $table);
            $this->addAlias($alias);
        } else {
            $alias = null;
        }
        return [$table, $alias];
    }
}
