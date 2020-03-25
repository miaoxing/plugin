<?php

namespace Miaoxing\Plugin\Service;

use Closure;
use Miaoxing\Services\Service\StaticTrait;
use Wei\Base;

/**
 * A SQL query builder class
 *
 * @author      Twin Huang <twinhuang@qq.com>
 * @property    \Wei\Db $db A database service inspired by Doctrine DBAL
 */
class QueryBuilder extends Base
{
    use StaticTrait;

    /* The query types. */
    const SELECT = 0;
    const DELETE = 1;
    const UPDATE = 2;

    /* The builder states. */
    const STATE_DIRTY = 0;
    const STATE_CLEAN = 1;

    /**
     * The record table name
     *
     * @var string
     */
    protected $table;

    /**
     * The complete record table name with table prefix
     *
     * @var string
     */
    protected $fullTable;

    /**
     * The table fields
     * If leave it blank, it will automatic generate form the database table,
     * or fill it to speed up the record
     *
     * @var array
     */
    protected $fields = array();

    /**
     * The primary key field
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The parts of SQL
     *
     * @var array
     */
    protected $sqlParts = [
        'select' => [],
        'from' => null,
        'join' => [],
        'set' => [],
        'where' => null,
        'groupBy' => [],
        'having' => null,
        'orderBy' => [],
        'limit' => null,
        'offset' => null,
        'page' => null,
    ];

    /**
     * A field to be the key of the fetched array, if not provided, return
     * default number index array
     *
     * @var string
     */
    protected $indexBy;

    /**
     * @var string The complete SQL string for this query.
     */
    protected $sql;

    /**
     * The query parameters
     *
     * @var array
     */
    protected $params = array();

    /**
     * The parameter type map of this query
     *
     * @var array
     */
    protected $paramTypes = array();

    /**
     * The type of query this is. Can be select, update or delete
     *
     * @var integer
     */
    protected $type = self::SELECT;

    /**
     * The state of the query object. Can be dirty or clean
     *
     * @var integer
     */
    protected $state = self::STATE_CLEAN;

    /**
     * @var string|bool
     */
    protected $lock = '';

    /**
     * @param string|null $table
     * @return $this
     */
    public function __invoke(string $table = null)
    {
        return new static([
            'wei' => $this->wei,
            'db' => $this->db,
            'table' => $table,
        ]);
    }

    /**
     * Return the record table name
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set the record table name
     *
     * @param string $table
     * @return $this
     */
    public function setTable($table)
    {
        return $this->from($table);
    }

    /**
     * Sets the primary key field
     *
     * @param string $primaryKey
     * @return $this
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    /**
     * Returns the primary key field
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     */
    public function getFields()
    {
        if (empty($this->fields)) {
            $this->fields = $this->db->getTableFields($this->fullTable, true);
        }
        return $this->fields;
    }

    /**
     * Get the state of this query builder instance
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Execute this query using the bound parameters and their types
     *
     * @return mixed
     */
    public function execute()
    {
        if ($this->type == self::SELECT) {
            $this->loaded = true;
            if ($this->cacheTime !== false) {
                return $this->fetchFromCache();
            } else {
                return $this->db->fetchAll($this->getSql(), $this->params, $this->paramTypes);
            }
        } else {
            return $this->db->executeUpdate($this->getSql(), $this->params, $this->paramTypes);
        }
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed $conditions
     * @return array|false
     */
    public function fetch($column, $operator = null, $value = null)
    {
        $this->where(...func_get_args());
        $this->limit(1);
        $data = $this->execute();
        return $data ? $data[0] : false;
    }

    /**
     * Executes the generated query and returns a column value of the first row
     *
     * @param mixed $conditions
     * @return array|false
     */
    public function fetchColumn($column, $operator = null, $value = null)
    {
        $data = $this->fetch(...func_get_args());
        return $data ? current($data) : false;
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed $conditions
     * @return array|false
     */
    public function fetchAll($column, $operator = null, $value = null)
    {
        $this->where(...func_get_args());
        $data = $this->execute();
        if ($this->indexBy) {
            $data = $this->executeIndexBy($data, $this->indexBy);
        }
        return $data;
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param mixed $conditions
     * @param string $count
     * @return int
     */
    public function count($conditions = false, $count = '1')
    {
        $this->where(...func_get_args());

        $select = $this->sqlParts['select'];
        $this->select('COUNT(' . $count . ')');
        $count = (int) $this->db->fetchColumn($this->getSqlForSelect(true), $this->params);
        $this->sqlParts['select'] = $select;

        return $count;
    }

    /**
     * Executes a sub query to receive the rows number
     *
     * @param mixed $conditions
     * @return int
     */
    public function countBySubQuery($column, $operator = null, $value = null)
    {
        $this->where(...func_get_args());
        return (int) $this->db->fetchColumn($this->getSqlForCount(), $this->params);
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @return int
     */
    public function update($set = array())
    {
        if (is_array($set)) {
            $params = array();
            foreach ($set as $field => $param) {
                $this->add('set', $field . ' = ?', true);
                $params[] = $param;
            }
            $this->params = array_merge($params, $this->params);
        } else {
            $this->add('set', $set, true);
        }
        $this->type = self::UPDATE;
        return $this->execute();
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed $conditions
     * @return mixed
     */
    public function delete($column, $operator = null, $value = null)
    {
        $this->where(...func_get_args());
        $this->type = self::DELETE;
        return $this->execute();
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param integer $offset The first result to return
     * @return $this
     */
    public function offset($offset)
    {
        $offset = (int) $offset;
        $offset < 0 && $offset = 0;
        return $this->add('offset', $offset);
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param integer $limit The maximum number of results to retrieve
     * @return $this
     */
    public function limit($limit)
    {
        $limit = max(1, (int) $limit);
        $this->add('limit', $limit);

        // 计算出新的offset
        if ($page = $this->getSqlPart('page')) {
            $this->page($page);
        }

        return $this;
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     */
    public function page($page)
    {
        $page = max(1, (int) $page);
        $this->add('page', $page);

        $limit = $this->getSqlPart('limit');
        if (!$limit) {
            $limit = 10;
            $this->add('limit', $limit);
        }
        return $this->offset(($page - 1) * $limit);
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param string|array $columns The selection expressions.
     * @return $this
     */
    public function select($columns = ['*']): self
    {
        $this->type = self::SELECT;

        $columns = is_array($columns) ? $columns : func_get_args();

        return $this->add('select', (array) $columns, true);
    }

    /**
     * @param bool $distinct
     * @return $this
     */
    public function distinct(bool $distinct = true)
    {
        return $this->add('distinct', $distinct);
    }

    public function selectDistinct($columns)
    {
        $this->distinct(true);
        return $this->select(func_get_args());
    }

    public function selectRaw($expression)
    {
        $this->type = self::SELECT;

        return $this->add('select', $this->raw($expression));
    }

    public function raw($expression)
    {
        return (object) $expression;
    }

    protected function getRawValue($expression)
    {
        return $expression->scalar;
    }

    protected function isRaw($expression)
    {
        return $expression instanceof \stdClass && isset($expression->scalar);
    }

    /**
     * Sets table for FROM query
     *
     * @param string $from The table
     * @return $this
     */
    public function from($from)
    {
        $pos = strpos($from, ' ');
        if (false !== $pos) {
            $this->table = substr($from, 0, $pos);
        } else {
            $this->table = $from;
        }
        $this->fullTable = $this->db->getTable($this->table);
        return $this->add('from', $this->db->getTable($from));
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string $on The condition for the join
     * @return $this
     */
    public function join($table, $on = null)
    {
        return $this->innerJoin($table, $on);
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string $on The condition for the join
     * @return $this
     */
    public function innerJoin($table, $on = null)
    {
        return $this->add('join', array('type' => 'inner', 'table' => $table, 'on' => $on), true);
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string $on The condition for the join
     * @return $this
     */
    public function leftJoin($table, $on = null)
    {
        return $this->add('join', array('type' => 'left', 'table' => $table, 'on' => $on), true);
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string $on The condition for the join
     * @return $this
     */
    public function rightJoin($table, $on = null)
    {
        return $this->add('join', array('type' => 'right', 'table' => $table, 'on' => $on), true);
    }

    /**
     * Specifies one or more restrictions to the query result.
     * Replaces any previously specified restrictions, if any.
     *
     * ```php
     * $user = wei()->db('user')->where('id = 1');
     * $user = wei()->db('user')->where('id = ?', 1);
     * $users = wei()->db('user')->where(array('id' => '1', 'username' => 'twin'));
     * $users = wei()->where(array('id' => array('1', '2', '3')));
     * ```
     *
     * @param string|array $column
     * @param null $operator
     * @param null $value
     * @return $this
     */
    public function where($column, $operator = null, $value = null)
    {
        if (is_array($column)) {
            foreach ($column as $arg) {
                $this->where(...$arg);
            }
            return $this;
        }

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        return $this->addWhere($column, $operator, $value, 'AND');
    }

    public function whereRaw($expression, $params = [])
    {
        return $this->where($this->raw($expression), null, $params);
    }

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * disjunction with any previously specified restrictions.
     *
     * @param string $conditions The WHERE conditions
     * @param array $params The condition parameters
     * @param array $types The parameter types
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        if (is_array($column)) {
            foreach ($column as $arg) {
                $this->orWhere(...$arg);
            }
            return $this;
        }

        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        return $this->addWhere($column, $operator, $value, 'OR');
    }

    public function orWhereRaw($expression, $params = null)
    {
        return $this->orWhere($this->raw($expression), null, $params);
    }

    public function whereBetween($column, array $params)
    {
        return $this->addWhere($column, 'BETWEEN', $params);
    }

    public function orWhereBetween($column, array $params)
    {
        return $this->addWhere($column, 'BETWEEN', $params, 'OR');
    }

    public function whereNotBetween($column, array $params)
    {
        return $this->addWhere($column, 'NOT BETWEEN', $params);
    }

    public function orWhereNotBetween($column, array $params)
    {
        return $this->addWhere($column, 'NOT BETWEEN', $params, 'OR');
    }

    public function whereIn($column, array $params)
    {
        return $this->addWhere($column, 'IN', $params);
    }

    public function orWhereIn($column, array $params)
    {
        return $this->addWhere($column, 'IN', $params, 'OR');
    }

    public function whereNotIn($column, array $params)
    {
        return $this->addWhere($column, 'NOT IN', $params);
    }

    public function orWhereNotIn($column, array $params)
    {
        return $this->addWhere($column, 'NOT IN', $params, 'OR');
    }

    public function whereNull($column)
    {
        return $this->addWhere($column, 'NULL');
    }

    public function orWhereNull($column)
    {
        return $this->addWhere($column, 'NULL', null, 'OR');
    }

    public function whereNotNULL($column)
    {
        return $this->addWhere($column, 'NOT NULL');
    }

    public function orWhereNotNull($column)
    {
        return $this->addWhere($column, 'NOT NULL', null, 'OR');
    }

    public function whereDate($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'DATE');
    }

    public function orWhereDate($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'DATE');
    }

    public function whereMonth($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'MONTH');
    }

    public function orWhereMonth($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'MONTH');
    }

    public function whereDay($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'DAY');
    }

    public function orWhereDay($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'DAY');
    }

    public function whereYear($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'YEAR');
    }

    public function orWhereYear($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'YEAR');
    }

    public function whereTime($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'TIME');
    }

    public function orWhereTime($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'TIME');
    }

    public function whereColumn($column, $opOrColumn2, $column2 = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'COLUMN');
    }

    public function orWhereColumn($column, $opOrColumn2, $column2 = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'COLUMN');
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     */
    public function whereContains($column, $value, string $condition = 'AND')
    {
        return $this->addWhere($column, 'LIKE', '%' . $value . '%', $condition);
    }

    public function orWhereContains($column, $value)
    {
        return $this->whereContains($column, $value, 'OR');
    }

    public function whereNotContains($column, $value, string $condition = 'OR')
    {
        return $this->addWhere($column, 'NOT LIKE', '%' . $value . '%', $condition);
    }

    public function orWhereNotContains($column, $value)
    {
        return $this->whereNotContains($column, $value, 'OR');
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column The grouping column.
     * @return $this
     */
    public function groupBy($column)
    {
        $column = is_array($column) ? $column : func_get_args();
        return $this->add('groupBy', $column, true);
    }

    /**
     * Specifies a restriction over the groups of the query.
     * Replaces any previous having restrictions, if any.
     *
     * @param string $conditions The having conditions
     * @param array $params The condition parameters
     * @param array $types The parameter types
     * @return $this
     */
    public function having($column, $operator, $value = null, $condition = 'AND')
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        $this->sqlParts['having'][] = compact('column', 'operator', 'value', 'condition');
        return $this;
    }

    public function havingRaw($expression, $params = [])
    {
        return $this->having($this->raw($expression), null, $params);
    }

    /**
     * Adds a restriction over the groups of the query, forming a logical
     * disjunction with any existing having restrictions.
     *
     * @param string $conditions The HAVING conditions to add
     * @param array $params The condition parameters
     * @param array $types The parameter types
     * @return $this
     */
    public function orHaving($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        return $this->having($column, $operator, $value, 'OR');
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column The ordering expression.
     * @param string $order The ordering direction.
     * @return $this
     */
    public function orderBy($column, $order = 'ASC')
    {
        $order = strtoupper($order);
        if (!in_array($order, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException('Parameter for "order" must be "ASC" or "DESC".');
        }

        return $this->add('orderBy', [compact('column', 'order')], true);
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     */
    public function desc($field)
    {
        return $this->orderBy($field, 'DESC');
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     */
    public function asc($field)
    {
        return $this->orderBy($field, 'ASC');
    }

    /**
     * Specifies a field to be the key of the fetched array
     *
     * @param string $field
     * @return $this
     */
    public function indexBy($field)
    {
        $this->data = $this->executeIndexBy($this->data, $field);
        $this->indexBy = $field;
        return $this;
    }

    /**
     * @param array $data
     * @param string $field
     * @return array
     */
    protected function executeIndexBy($data, $field)
    {
        if (!$data) {
            return $data;
        }

        $newData = array();
        foreach ($data as $row) {
            $newData[$row[$field]] = $row;
        }
        return $newData;
    }

    /**
     * Returns a SQL query part by its name
     *
     * @param string $name The name of SQL part
     * @return mixed
     */
    public function getSqlPart($name)
    {
        return isset($this->sqlParts[$name]) ? $this->sqlParts[$name] : false;
    }

    /**
     * Get all SQL parts
     *
     * @return array $sqlParts
     */
    public function getSqlParts()
    {
        return $this->sqlParts;
    }

    /**
     * Reset all SQL parts
     *
     * @param array $name
     * @return $this
     */
    public function resetSqlParts($name = null)
    {
        if (is_null($name)) {
            $name = array_keys($this->sqlParts);
        }
        foreach ($name as $queryPartName) {
            $this->resetSqlPart($queryPartName);
        }
        return $this;
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     */
    public function resetSqlPart($name)
    {
        $this->sqlParts[$name] = is_array($this->sqlParts[$name]) ? array() : null;
        $this->state = self::STATE_DIRTY;
        return $this;
    }

    /**
     * Sets a query parameter for the query being constructed
     *
     * @param string|integer $key The parameter position or name
     * @param mixed $value The parameter value
     * @param string|null $type PDO::PARAM_*
     * @return $this
     */
    public function setParameter($key, $value, $type = null)
    {
        if ($type !== null) {
            $this->paramTypes[$key] = $type;
        }

        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Gets a (previously set) query parameter of the query being constructed
     *
     * @param mixed $key The key (index or name) of the bound parameter
     * @return mixed The value of the bound parameter
     */
    public function getParameter($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * Sets a collection of query parameters for the query being constructed
     *
     * @param array $params The query parameters to set
     * @param array $types The query parameters types to set
     * @return $this
     */
    public function setParameters(array $params, array $types = array())
    {
        $this->paramTypes = $types;
        $this->params = $params;
        return $this;
    }

    /**
     * Gets all defined query parameters for the query being constructed.
     *
     * @return array The currently defined query parameters.
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * Get the complete SQL string formed by the current specifications of this QueryBuilder
     *
     * @return string The sql query string
     */
    public function getSql()
    {
        if ($this->sql !== null && $this->state === self::STATE_CLEAN) {
            return $this->sql;
        }

        switch ($this->type) {
            case self::DELETE:
                $this->sql = $this->getSqlForDelete();
                break;

            case self::UPDATE:
                $this->sql = $this->getSqlForUpdate();
                break;

            case self::SELECT:
            default:
                $this->sql = $this->getSqlForSelect();
                break;
        }

        $this->state = self::STATE_CLEAN;

        return $this->sql;
    }

    /**
     * Returns the interpolated query.
     *
     * @return string
     * @link https://stackoverflow.com/a/8403150
     */
    public function getRawSql()
    {
        $query = $this->getSql();
        $keys = [];
        $values = $this->params;

        // build a regular expression for each parameter
        foreach ($this->params as $key => $value) {
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
     * @param bool $count
     * @return string
     */
    protected function getSqlForSelect($count = false)
    {
        $parts = $this->sqlParts;

        if (!$parts['select']) {
            $parts['select'] = array('*');
        }

        $query = 'SELECT ';

        if (isset($parts['distinct']) && $parts['distinct']) {
            $query .= 'DISTINCT ';
        }

        $selects = [];
        foreach ($parts['select'] as $as => $select) {
            if ($this->isRaw($select)) {
                $selects[] = $this->getRawValue($select);
            } elseif (is_string($as)) {
                $selects[] = $this->wrap($as) . ' AS ' . $this->wrap($select);
            } else {
                $selects[] = $select === '*' ? '*' : $this->wrap($select);
            }
        }
        $query .= implode(', ', $selects);

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

        if (false === $count) {
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
                $this->addParams($where['value']);
                continue;
            }

            if ($where['column'] instanceof Closure) {
                $prevCount = count($this->sqlParts['where']);
                $where['column']($this);
                $newWhere = array_slice($this->sqlParts['where'], $prevCount);
                $query .= '(' . $this->buildWhere($newWhere) . ')';
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
                    $query .= $this->processCondition($column . ' ' . $where['operator'] . ' ? AND ?',
                        $where['value']);
                    break;

                case 'IN':
                case 'NOT IN':
                    $query .= $this->processCondition($column . ' ' . $where['operator']
                        . ' (' . implode(', ', array_pad([], count($where['value']), '?')) . ')', $where['value']);
                    break;

                case 'NULL':
                case 'NOT NULL':
                    $query .= $this->processCondition($column . ' IS ' . $where['operator']);
                    break;

                default:
                    $query .= $this->processCondition($column . ' ' . ($where['operator'] ?: '=') . ' ?',
                        $where['value']);
            }
        }

        return $query;
    }

    protected function addParams($params)
    {
        if ($params !== false) {
            if (is_array($params)) {
                $this->params = array_merge($this->params, $params);
            } else {
                $this->params[] = $params;
            }
        }
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
            . ' SET ' . implode(", ", $this->sqlParts['set'])
            . ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '');
        return $query;
    }

    /**
     * Converts this instance into a DELETE string in SQL.
     *
     * @return string
     */
    protected function getSqlForDelete()
    {
        return 'DELETE FROM ' . $this->getFrom() . ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string) $this->sqlParts['where']) : '');
    }

    /**
     * Returns the from SQL part
     *
     * @return string
     */
    protected function getFrom()
    {
        if (!$this->sqlParts['from']) {
            $this->from($this->table);
        }
        return $this->sqlParts['from'];
    }

    /**
     * Either appends to or replaces a single, generic query part.
     *
     * The available parts are: 'select', 'from', 'set', 'where',
     * 'groupBy', 'having', 'orderBy', 'limit' and 'offset'.
     *
     * @param string $sqlPartName
     * @param mixed $sqlPart
     * @param boolean $append
     * @param string $type
     * @return $this
     */
    protected function add($sqlPartName, $sqlPart, $append = false, $type = null)
    {
        $this->isNew = false;
        $this->state = self::STATE_DIRTY;

        $isArray = is_array($sqlPart);
        $isMultiple = is_array($this->sqlParts[$sqlPartName]);

        if ($isMultiple && !$isArray) {
            $sqlPart = array($sqlPart);
        }

        if ($append) {
            if ($sqlPartName == 'where' || $sqlPartName == 'having') {
                if ($this->sqlParts[$sqlPartName]) {
                    $this->sqlParts[$sqlPartName] = '(' . $this->sqlParts[$sqlPartName] . ') ' . $type . ' (' . $sqlPart . ')';
                } else {
                    $this->sqlParts[$sqlPartName] = $sqlPart;
                }
            } elseif ($sqlPartName == 'orderBy' || $sqlPartName == 'groupBy' || $sqlPartName == 'select' || $sqlPartName == 'set') {
                $this->sqlParts[$sqlPartName] = array_merge($this->sqlParts[$sqlPartName], $sqlPart);
            } elseif ($isMultiple) {
                $this->sqlParts[$sqlPartName][] = $sqlPart;
            }
            return $this;
        }

        $this->sqlParts[$sqlPartName] = $sqlPart;
        return $this;
    }

    /**
     * Generate condition string for WHERE or Having statement
     *
     * @param mixed $conditions
     * @param array $params
     * @param array $types
     * @return string
     */
    protected function processCondition($conditions, $params = [])
    {
        // Regard numeric and null as primary key value
        if (is_numeric($conditions) || empty($conditions)) {
            $conditions = array($this->primaryKey => $conditions);
        }

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

        $this->addParams($params);

        return $conditions;
    }

    protected function addWhere($column, $operator, $value = null, $condition = 'AND', $type = null)
    {
        $this->sqlParts['where'][] = compact('column', 'operator', 'value', 'condition', 'type');
        return $this;
    }

    protected function addWhereArgs($args, $condition = 'AND', $type = null)
    {
        if (count($args) === 2) {
            $operator = '=';
            [$column, $value] = $args;
        } else {
            [$column, $operator, $value] = $args;
        }
        return $this->addWhere($column, $operator, $value, $condition, $type);
    }

    /**
     * @return $this
     */
    public function forUpdate()
    {
        return $this->lock(true);
    }

    /**
     * @return $this
     */
    public function forShare()
    {
        return $this->lock(false);
    }

    /**
     * @param string $lock
     * @return $this
     */
    public function lock($lock)
    {
        $this->lock = $lock;

        return $this;
    }

    /**
     * @return string
     */
    protected function generateLockSql()
    {
        if ($this->lock === '') {
            return '';
        }

        if (is_string($this->lock)) {
            return ' ' . $this->lock;
        }

        if ($this->lock) {
            return ' FOR UPDATE';
        } else {
            return ' LOCK IN SHARE MODE';
        }
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     */
    public function when($value, $callback, callable $default = null)
    {
        if ($value) {
            $callback($this, $value);
        } elseif ($default) {
            $default($this, $value);
        }
        return $this;
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     */
    public function unless($value, callable $callback, callable $default = null)
    {
        if (!$value) {
            $callback($this, $value);
        } elseif ($default) {
            $default($this, $value);
        }
        return $this;
    }

    protected function wrap($value)
    {
        return '`' . $value . '`';
    }
}
