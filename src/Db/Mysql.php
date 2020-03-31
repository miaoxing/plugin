<?php

namespace Miaoxing\Plugin\Db;

use Miaoxing\Plugin\Service\QueryBuilder;

class Mysql extends BaseDriver
{
    /* The query types. */
    const SELECT = 0;
    const DELETE = 1;
    const UPDATE = 2;

    protected $wrapper = '`';

    protected $sqlParts = [];

    public function getSql($type, $sqlParts)
    {
        $this->sqlParts = $sqlParts;
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
     * @return string
     * @link https://stackoverflow.com/a/8403150
     */
    public function getRawSql($type, $sqlParts, array $values)
    {
        $query = $this->getSql($type, $sqlParts);
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
            } elseif ($value === null) {
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
            $parts['select'] = array('*');
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

        $query .= ' FROM ' . $this->wrap($this->getFrom());

        // JOIN
        foreach ($parts['join'] as $join) {
            $query .= ' ' . strtoupper($join['type'])
                . ' JOIN ' . $join['table']
                . ' ON ' . $join['on'];
        }

        if ($parts['where']) {
            $query .= ' WHERE ' . $this->buildWhere($parts['where']);
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
            $query .= ' HAVING ' . $this->buildWhere($parts['having']);
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

            $query .= ($parts['limit'] !== null ? ' LIMIT ' . $parts['limit'] : '')
                . ($parts['offset'] !== null ? ' OFFSET ' . $parts['offset'] : '');
        }

        $query .= $this->generateLockSql();

        return $query;
    }

    protected function buildWhere(array $wheres)
    {
        $query = '';
        foreach ($wheres as $i => $where) {
            if ($i !== 0) {
                $query .= ' ' . $where['condition'] . ' ';
            }

            if ($this->isRaw($where['column'])) {
                $query .= $this->getRawValue($where['column']);
                continue;
            }

            if ($where['column'] instanceof QueryBuilder) {
                $sqlParts = $where['column']->getSqlParts();
                $query .= '(' . $this->buildWhere($sqlParts['where']) . ')';
                continue;
            }

            $column = $this->wrap($where['column']);
            switch ($where['type']) {
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
                    $query .= $this->processCondition($column . ' ' . $where['operator'] . ' (' . implode(', ',
                            array_pad([], count($where['value']), '?')) . ')');
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
        return "SELECT COUNT(*) FROM (" . $this->getSqlForSelect(true) . ") wei_count";
    }

    /**
     * Converts this instance into an UPDATE string in SQL.
     *
     * @return string
     */
    protected function getSqlForUpdate()
    {
        $query = 'UPDATE ' . $this->getFrom()
            . ' SET ' . implode(', ', $this->sqlParts['set'])
            . ($this->sqlParts['where'] ? ' WHERE ' . $this->buildWhere($this->sqlParts['where']) : '');
        return $query;
    }

    /**
     * Converts this instance into a DELETE string in SQL.
     *
     * @return string
     */
    protected function getSqlForDelete()
    {
        return 'DELETE FROM ' . $this->getFrom() . ($this->sqlParts['where'] ? ' WHERE ' . $this->buildWhere($this->sqlParts['where']) : '');
    }

    /**
     * Returns the from SQL part
     *
     * @return string
     */
    protected function getFrom()
    {
        return $this->db->getTable($this->sqlParts['from']);
    }

    /**
     * Generate condition string for WHERE or Having statement
     *
     * @param mixed $conditions
     * @param array $params
     * @param array $types
     * @return string
     */
    protected function processCondition($conditions)
    {
        if (is_array($conditions)) {
            $where = array();
            $params = array();
            foreach ($conditions as $field => $condition) {
                if (is_array($condition)) {
                    $where[] = $field . ' IN (' . implode(', ', array_pad(array(), count($condition), '?')) . ')';
                    $params = array_merge($params, $condition);
                } else {
                    $where[] = $field . " = ?";
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
        if ($this->sqlParts['lock'] === '') {
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
}
