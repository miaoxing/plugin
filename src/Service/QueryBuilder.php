<?php

namespace Miaoxing\Plugin\Service;

use Closure;
use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Db\BaseDriver;
use Miaoxing\Plugin\Model\QueryBuilderCacheTrait;

/**
 * A SQL query builder class
 *
 * @author Twin Huang <twinhuang@qq.com>
 * @mixin \DbMixin
 * @mixin \TagCacheMixin
 */
class QueryBuilder extends BaseService
{
    use QueryBuilderCacheTrait;

    // The query types.

    const SELECT = 0;
    const DELETE = 1;
    const UPDATE = 2;

    // The builder states.

    const STATE_DIRTY = 0;
    const STATE_CLEAN = 1;

    /**
     * @var bool
     */
    protected static $createNewInstance = true;

    /**
     * The record table name
     *
     * @var string
     */
    protected $table;

    /**
     * The table fields
     * If leave it blank, it will automatic generate form the database table,
     * or fill it to speed up the record
     *
     * @var array
     */
    protected $fields = [];

    /**
     * The primary key field
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var callable
     */
    protected $inputIdentifierConverter = [self::class, 'snake'];

    /**
     * @var callable
     */
    protected $outputIdentifierConverter = [self::class, 'camel'];

    /**
     * The parts of SQL
     *
     * @var array
     */
    protected $sqlParts = [
        'select' => [],
        'distinct' => null,
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
        'lock' => '',
        'aggregate' => null,
    ];

    /**
     * The query parameters
     *
     * @var array
     */
    protected $params = [
        'set' => [],
        'where' => [],
    ];

    /**
     * The parameter type map of this query
     *
     * @var array
     */
    protected $paramTypes = [];

    /**
     * A field to be the key of the fetched array, if not provided, return
     * default number index array
     *
     * @var string
     */
    protected $indexBy;

    /**
     * @var string the complete SQL string for this query
     */
    protected $sql;

    /**
     * The type of query this is. Can be select, update or delete
     *
     * @var int
     */
    protected $type = self::SELECT;

    /**
     * The state of the query object. Can be dirty or clean
     *
     * @var int
     */
    protected $state = self::STATE_CLEAN;

    /**
     * @var BaseDriver[]
     */
    protected static $drivers = [];

    /**
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * @var array
     */
    protected static $camelCache = [];

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
     * Get the state of this query builder instance
     *
     * @return int
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
        if (self::SELECT == $this->type) {
            $this->loaded = true;
            if (false !== $this->cacheTime) {
                return $this->fetchFromCache();
            } else {
                return $this->db->fetchAll($this->getSql(), $this->getBindParams(), $this->paramTypes);
            }
        } else {
            return $this->db->executeUpdate($this->getSql(), $this->getBindParams(), $this->paramTypes);
        }
    }

    /**
     * Executes the generated query and returns a column value of the first row
     *
     * @param mixed $conditions
     * @param mixed|null $colum
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     */
    public function fetchColumn($colum = null, $operator = null, $value = null)
    {
        $data = $this->fetch(...func_get_args());
        return $data ? current($data) : null;
    }

    /**
     * Executes a sub query to receive the rows number
     *
     * @param mixed $conditions
     * @return int
     * @todo 改为自动识别
     */
    public function countBySubQuery()
    {
        $this->where(...func_get_args());
        return (int) $this->db->fetchColumn($this->getSqlForCount(), $this->getBindParams());
    }

    public function max($column)
    {
        return $this->aggregate('MAX', $column);
    }

    public function aggregate($function, $columns = ['*'])
    {
        $this->add('aggregate', compact('function', 'columns'));
        return $this->fetchColumn(null);
    }

    /**
     * @param bool $distinct
     * @return $this
     */
    public function distinct(bool $distinct = true)
    {
        return $this->add('distinct', $distinct);
    }

    public function raw($expression)
    {
        return (object) $expression;
    }

    /**
     * Adds one or more restrictions to the query results, forming a logical
     * disjunction with any previously specified restrictions.
     *
     * @param string $conditions The WHERE conditions
     * @param array $params The condition parameters
     * @param array $types The parameter types
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
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

        if (2 === func_num_args()) {
            $value = $operator;
            $operator = '=';
        }

        return $this->addWhere($column, $operator, $value, 'OR');
    }

    public function orWhereRaw($expression, $params = null)
    {
        return $this->orWhere($this->raw($expression), null, $params);
    }

    public function orWhereNotBetween($column, array $params)
    {
        return $this->addWhere($column, 'NOT BETWEEN', $params, 'OR');
    }

    public function orWhereIn($column, array $params)
    {
        return $this->addWhere($column, 'IN', $params, 'OR');
    }

    public function orWhereNotIn($column, array $params)
    {
        return $this->addWhere($column, 'NOT IN', $params, 'OR');
    }

    public function orWhereNull($column)
    {
        return $this->addWhere($column, 'NULL', null, 'OR');
    }

    public function orWhereNotNull($column)
    {
        return $this->addWhere($column, 'NOT NULL', null, 'OR');
    }

    public function orWhereDate($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'DATE');
    }

    public function orWhereMonth($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'MONTH');
    }

    public function orWhereDay($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'DAY');
    }

    public function orWhereYear($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'YEAR');
    }

    public function orWhereTime($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'TIME');
    }

    public function orWhereColumn($column, $opOrColumn2, $column2 = null)
    {
        return $this->addWhereArgs(func_get_args(), 'OR', 'COLUMN');
    }

    public function orWhereContains($column, $value)
    {
        return $this->whereContains($column, $value, 'OR');
    }

    public function orWhereNotContains($column, $value)
    {
        return $this->whereNotContains($column, $value, 'OR');
    }

    /**
     * @param $expression
     * @param array $params
     * @return $this
     * @svc
     */
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
     * @param mixed $column
     * @param mixed $operator
     * @param mixed|null $value
     * @return $this
     */
    public function orHaving($column, $operator, $value = null)
    {
        if (2 === func_num_args()) {
            $value = $operator;
            $operator = '=';
        }
        return $this->having($column, $operator, $value, 'OR');
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
        if (null === $name) {
            $name = array_keys($this->sqlParts);
        }
        foreach ($name as $queryPartName) {
            $this->resetSqlPart($queryPartName);
        }
        return $this;
    }

    /**
     * Sets a query parameter for the query being constructed
     *
     * @param int|string $key The parameter position or name
     * @param mixed $value The parameter value
     * @param string|null $type PDO::PARAM_*
     * @return $this
     * @todo refactor 暂不支持
     */
    public function setParameter($key, $value, $type = null)
    {
        if (null !== $type) {
            $this->paramTypes[$key] = $type;
        }

        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Gets a (previously set) query parameter of the query being constructed
     *
     * @param mixed $key The key (index or name) of the bound parameter
     * @param string $type
     * @return mixed The value of the bound parameter
     */
    public function getParameter($key, $type = 'where')
    {
        return $this->getBindParams()[$key] ?? null;
    }

    /**
     * Sets a collection of query parameters for the query being constructed
     *
     * @param array $params The query parameters to set
     * @param array $types The query parameters types to set
     * @return $this
     */
    public function setParameters(array $params, array $types = [])
    {
        $this->paramTypes = $types;
        $this->params = $params;
        return $this;
    }

    /**
     * Gets all defined query parameters for the query being constructed.
     *
     * @return array the currently defined query parameters
     */
    public function getParameters()
    {
        return $this->params;
    }

    /**
     * @param array|string $parameter
     * @param string $type
     * @return $this
     */
    public function addParameter($parameter, $type = 'where')
    {
        $this->params[$type][] = (array) $parameter;
        return $this;
    }

    public function removeParameters($type = null)
    {
        if ($type) {
            $this->params[$type] = [];
        } else {
            foreach ($this->params as $paramType => $params) {
                $this->params[$paramType] = [];
            }
        }
        return $this;
    }

    /**
     * Get the complete SQL string formed by the current specifications of this QueryBuilder
     *
     * @return string The sql query string
     */
    public function getSql()
    {
        if (null !== $this->sql && self::STATE_CLEAN === $this->state) {
            return $this->sql;
        }

        if (!$this->sqlParts['from']) {
            $this->sqlParts['from'] = $this->getTable();
        }

        $this->sql = $this->getDriver()->getSql($this->type, $this->sqlParts, $this->inputIdentifierConverter);

        $this->state = self::STATE_CLEAN;

        return $this->sql;
    }

    public function getRawSql()
    {
        return $this->getDriver()->getRawSql(
            $this->type,
            $this->sqlParts,
            $this->inputIdentifierConverter,
            $this->getBindParams()
        );
    }

    /**
     * Returns flatten array for parameter binding.
     *
     * @return array
     */
    public function getBindParams()
    {
        $params = [];
        foreach ($this->params as $value) {
            $params[] = array_merge([], ...$value);
        }
        return array_merge([], ...$params);
    }

    /**
     * Reset all SQL parts and parameters
     *
     * @return $this
     */
    public function resetQuery()
    {
        $this->params = [];
        $this->paramTypes = [];

        return $this->resetSqlParts();
    }

    /**
     * @return callable
     */
    public function getInputIdentifierConverter()
    {
        return $this->inputIdentifierConverter;
    }

    /**
     * Return the record table name
     *
     * @return string
     * @svc
     */
    protected function getTable()
    {
        return $this->table;
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @svc
     */
    protected function getFields()
    {
        if (empty($this->fields)) {
            $this->fields = $this->db->getTableFields($this->getTable());
        }
        return $this->fields;
    }

    /**
     * @return mixed
     */
    protected function fetchFromCache()
    {
        $cache = false === $this->cacheTags ? $this->cache : $this->tagCache($this->cacheTags ?: $this->getCacheTags());
        return $cache->get($this->getCacheKey(), $this->cacheTime, function () {
            return $this->db->fetchAll($this->getSql(), $this->getBindParams(), $this->paramTypes);
        });
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed $conditions
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @svc
     */
    protected function fetch($column = null, $operator = null, $value = null)
    {
        $this->where(...func_get_args());
        $this->limit(1);
        $data = $this->execute();
        return $data ? $data[0] : null;
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed $conditions
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|false
     * @svc
     */
    protected function fetchAll($column = null, $operator = null, $value = null)
    {
        $this->where(...func_get_args());
        $data = $this->execute();
        if ($this->indexBy) {
            $data = $this->executeIndexBy($data, $this->indexBy);
        }
        return $data;
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return array|null
     * @svc
     */
    protected function first()
    {
        return $this->fetch();
    }

    /**
     * @return array|null
     * @svc
     */
    protected function all()
    {
        return $this->fetchAll();
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @svc
     */
    protected function pluck(string $column, string $index = null)
    {
        $columns = [$column];
        $index && $columns[] = $index;
        $data = $this->select($columns)->fetchAll();
        return array_column($data, $column, $index);
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @svc
     */
    protected function chunk(int $count, callable $callback)
    {
        $this->limit($count);
        $page = 1;

        do {
            $qb = clone $this;
            $data = $qb->page($page)->all();

            // Do not execute callback when no new records are founded
            if (0 === count($data)) {
                break;
            }

            if (false === $callback($data, $page)) {
                return false;
            }

            ++$page;
        } while (count($data) === $count);

        return true;
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @svc
     */
    protected function cnt($column = '*')
    {
        return (int) $this->aggregate('COUNT', $column);
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param null $value
     * @return int
     * @svc
     */
    protected function update($set = [], $value = null)
    {
        if (2 === func_num_args()) {
            $set = [$set => $value];
        }

        $params = [];
        foreach ($set as $field => $param) {
            $this->add('set', $field, true);
            $params[] = $param;
        }
        $this->params['set'][] = array_merge($this->params['set'], $params);

        $this->type = self::UPDATE;
        return $this->execute();
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed $conditions
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @svc
     */
    protected function delete($column = null, $operator = null, $value = null)
    {
        $this->where(...func_get_args());
        $this->type = self::DELETE;
        return $this->execute();
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int $offset The first result to return
     * @return $this
     * @svc
     */
    protected function offset($offset)
    {
        $offset = (int) $offset;
        $offset < 0 && $offset = 0;
        return $this->add('offset', $offset);
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int $limit The maximum number of results to retrieve
     * @return $this
     * @svc
     */
    protected function limit($limit)
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
     * @svc
     */
    protected function page($page)
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
     * @param array|string $columns the selection expressions
     * @return $this
     * @svc
     */
    protected function select($columns = ['*']): self
    {
        $this->type = self::SELECT;

        $columns = is_array($columns) ? $columns : func_get_args();

        return $this->add('select', (array) $columns, true);
    }

    /**
     * @param $columns
     * @return $this
     * @svc
     */
    protected function selectDistinct($columns)
    {
        $this->distinct(true);
        return $this->select(func_get_args());
    }

    /**
     * @param string $expression
     * @return $this
     * @svc
     */
    protected function selectRaw($expression)
    {
        $this->type = self::SELECT;

        return $this->add('select', $this->raw($expression));
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @svc
     */
    protected function selectExcept($columns)
    {
        $columns = array_diff($this->getFields(), is_array($columns) ? $columns : [$columns]);

        return $this->select($columns);
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @svc
     */
    protected function from($table, $alias = null): self
    {
        $this->table = $table;
        return $this->add('from', $table . ($alias ? ' ' . $alias : ''));
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @svc
     */
    protected function table(string $table, $alias = null): self
    {
        return $this->from($table, $alias);
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string $first
     * @param string $operator
     * @param string $second
     * @param string $type
     * @return $this
     * @svc
     */
    protected function join(
        string $table,
        string $first = null,
        string $operator = '=',
        string $second = null,
        string $type = 'INNER'
    ) {
        return $this->add('join', compact('table', 'first', 'operator', 'second', 'type'), true);
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @param string $type
     * @return $this
     * @svc
     */
    protected function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
        return $this->join(...func_get_args());
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @svc
     */
    protected function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @svc
     */
    protected function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
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
     * @param array|Closure|string|null $column
     * @param null $operator
     * @param null $value
     * @return $this
     * @svc
     */
    protected function where($column = null, $operator = null, $value = null)
    {
        if (null === $column) {
            return $this;
        }

        if (is_array($column)) {
            foreach ($column as $key => $args) {
                if (is_string($key)) {
                    $this->where($key, '=', $args);
                } else {
                    $this->where(...$args);
                }
            }
            return $this;
        }

        if (2 === func_num_args()) {
            $value = $operator;
            $operator = '=';
        }

        return $this->addWhere($column, $operator, $value, 'AND');
    }

    /**
     * @param string $expression
     * @param array $params
     * @return $this
     * @svc
     */
    protected function whereRaw($expression, $params = [])
    {
        return $this->where($this->raw($expression), null, $params);
    }

    /**
     * @param $column
     * @param array $params
     * @return $this
     * @svc
     */
    protected function whereBetween($column, array $params)
    {
        return $this->addWhere($column, 'BETWEEN', $params);
    }

    protected function orWhereBetween($column, array $params)
    {
        return $this->addWhere($column, 'BETWEEN', $params, 'OR');
    }

    /**
     * @param $column
     * @param array $params
     * @return $this
     * @svc
     */
    protected function whereNotBetween($column, array $params)
    {
        return $this->addWhere($column, 'NOT BETWEEN', $params);
    }

    /**
     * @param $column
     * @param array $params
     * @return $this
     * @svc
     */
    protected function whereIn($column, array $params)
    {
        return $this->addWhere($column, 'IN', $params);
    }

    /**
     * @param $column
     * @param array $params
     * @return $this
     * @svc
     */
    protected function whereNotIn($column, array $params)
    {
        return $this->addWhere($column, 'NOT IN', $params);
    }

    /**
     * @param $column
     * @return $this
     * @svc
     */
    protected function whereNull($column)
    {
        return $this->addWhere($column, 'NULL');
    }

    /**
     * @param $column
     * @return $this
     * @svc
     */
    protected function whereNotNULL($column)
    {
        return $this->addWhere($column, 'NOT NULL');
    }

    /**
     * @param $column
     * @param $opOrValue
     * @param null $value
     * @return $this
     * @svc
     */
    protected function whereDate($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'DATE');
    }

    /**
     * @param $column
     * @param $opOrValue
     * @param null $value
     * @return $this
     * @svc
     */
    protected function whereMonth($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'MONTH');
    }

    /**
     * @param $column
     * @param $opOrValue
     * @param null $value
     * @return $this
     * @svc
     */
    protected function whereDay($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'DAY');
    }

    /**
     * @param $column
     * @param $opOrValue
     * @param null $value
     * @return $this
     * @svc
     */
    protected function whereYear($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'YEAR');
    }

    /**
     * @param $column
     * @param $opOrValue
     * @param null $value
     * @return $this
     * @svc
     */
    protected function whereTime($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'TIME');
    }

    /**
     * @param $column
     * @param $opOrColumn2
     * @param null $column2
     * @return $this
     * @svc
     */
    protected function whereColumn($column, $opOrColumn2, $column2 = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'COLUMN');
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @svc
     */
    protected function whereContains($column, $value, string $condition = 'AND')
    {
        return $this->addWhere($column, 'LIKE', '%' . $value . '%', $condition);
    }

    /**
     * @param $column
     * @param $value
     * @param string $condition
     * @return $this
     * @svc
     */
    protected function whereNotContains($column, $value, string $condition = 'OR')
    {
        return $this->addWhere($column, 'NOT LIKE', '%' . $value . '%', $condition);
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @svc
     */
    protected function groupBy($column)
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
     * @param mixed $column
     * @param mixed $operator
     * @param mixed|null $value
     * @param mixed $condition
     * @return $this
     * @svc
     */
    protected function having($column, $operator, $value = null, $condition = 'AND')
    {
        if (2 === func_num_args()) {
            $value = $operator;
            $operator = '=';
        }

        if (null === $value) {
            $operator = 'NOT NULL' === $operator ? $operator : 'NULL';
        } else {
            $this->params['having'][] = (array) $value;
        }

        $this->sqlParts['having'][] = compact('column', 'operator', 'value', 'condition');

        return $this;
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @svc
     */
    protected function orderBy($column, $order = 'ASC')
    {
        $order = strtoupper($order);
        if (!in_array($order, ['ASC', 'DESC'], true)) {
            throw new \InvalidArgumentException('Parameter for "order" must be "ASC" or "DESC".');
        }

        return $this->add('orderBy', [compact('column', 'order')], true);
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @svc
     */
    protected function desc($field)
    {
        return $this->orderBy($field, 'DESC');
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @svc
     */
    protected function asc($field)
    {
        return $this->orderBy($field, 'ASC');
    }

    /**
     * Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @svc
     */
    protected function indexBy($column)
    {
        $this->indexBy = $column;
        return $this;
    }

    /**
     * @param array $data
     * @param string $column
     * @return array
     */
    protected function executeIndexBy($data, $column)
    {
        if (!$data) {
            return $data;
        }

        $newData = [];
        foreach ($data as $row) {
            $newData[$row[$column]] = $row;
        }
        return $newData;
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @svc
     */
    protected function resetSqlPart($name)
    {
        $this->sqlParts[$name] = is_array($this->sqlParts[$name]) ? [] : null;
        $this->state = self::STATE_DIRTY;
        return $this;
    }

    /**
     * Either appends to or replaces a single, generic query part.
     *
     * The available parts are: 'select', 'from', 'set', 'where',
     * 'groupBy', 'having', 'orderBy', 'limit' and 'offset'.
     *
     * @param string $sqlPartName
     * @param mixed $sqlPart
     * @param bool $append
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
            $sqlPart = [$sqlPart];
        }

        if ($append) {
            if ('orderBy' === $sqlPartName
                || 'groupBy' === $sqlPartName
                || 'select' === $sqlPartName
                || 'set' === $sqlPartName
            ) {
                $this->sqlParts[$sqlPartName] = array_merge($this->sqlParts[$sqlPartName], $sqlPart);
            } elseif ($isMultiple) {
                $this->sqlParts[$sqlPartName][] = $sqlPart;
            }
            return $this;
        }

        $this->sqlParts[$sqlPartName] = $sqlPart;
        return $this;
    }

    protected function addWhere($column, $operator, $value = null, $condition = 'AND', $type = null)
    {
        if ($column instanceof Closure) {
            $query = new static([
                'db' => $this->db,
                'table' => $this->table,
            ]);
            $column($query);
            $column = $query;
            $this->params['where'] = array_merge($this->params['where'], $query->getParameters()['where']);
        }

        if (null === $value) {
            $operator = 'NOT NULL' === $operator ? $operator : 'NULL';
        } elseif (is_array($value) && !in_array($operator, ['BETWEEN', 'NOT BETWEEN'], true)) {
            $operator = 'NOT IN' === $operator ? $operator : 'IN';
            $this->params['where'][] = (array) $value;
        } else {
            $this->params['where'][] = (array) $value;
        }

        $this->sqlParts['where'][] = compact('column', 'operator', 'value', 'condition', 'type');

        return $this;
    }

    protected function addWhereArgs($args, $condition = 'AND', $type = null)
    {
        if (2 === count($args)) {
            $operator = '=';
            [$column, $value] = $args;
        } else {
            [$column, $operator, $value] = $args;
        }
        return $this->addWhere($column, $operator, $value, $condition, $type);
    }

    /**
     * @return $this
     * @svc
     */
    protected function forUpdate()
    {
        return $this->lock(true);
    }

    /**
     * @return $this
     * @svc
     */
    protected function forShare()
    {
        return $this->lock(false);
    }

    /**
     * @param string $lock
     * @return $this
     * @svc
     */
    protected function lock($lock)
    {
        $this->sqlParts['lock'] = $lock;

        return $this;
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @svc
     */
    protected function when($value, $callback, callable $default = null)
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
     * @svc
     */
    protected function unless($value, callable $callback, callable $default = null)
    {
        if (!$value) {
            $callback($this, $value);
        } elseif ($default) {
            $default($this, $value);
        }
        return $this;
    }

    /**
     * @param callable $converter
     * @return $this
     * @svc
     */
    protected function setInputIdentifierConverter(callable $converter)
    {
        $this->inputIdentifierConverter = $converter;
        return $this;
    }

    /**
     * @param string $identifier
     * @return string
     */
    protected function convertInputIdentifier($identifier)
    {
        return $this->inputIdentifierConverter ? call_user_func(
            $this->inputIdentifierConverter,
            $identifier
        ) : $identifier;
    }

    /**
     * @param string $identifier
     * @return string
     */
    protected function convertOutputIdentifier($identifier)
    {
        return $this->outputIdentifierConverter ? call_user_func(
            $this->outputIdentifierConverter,
            $identifier
        ) : $identifier;
    }

    /**
     * @return BaseDriver
     */
    protected function getDriver()
    {
        $driver = $this->db->getDriver();
        if (!isset(static::$drivers[$driver])) {
            $class = 'Miaoxing\Plugin\Db\\' . ucfirst($driver);
            static::$drivers[$driver] = new $class();
        }
        return static::$drivers[$driver];
    }

    /**
     * Convert a input to snake case
     *
     * @param string $input
     * @return string
     */
    protected function snake($input)
    {
        if (isset(static::$snakeCache[$input])) {
            return static::$snakeCache[$input];
        }

        $value = $input;
        if (!ctype_lower($input)) {
            $value = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
        }

        return static::$snakeCache[$input] = $value;
    }

    /**
     * Convert a input to camel case
     *
     * @param string $input
     * @return string
     */
    protected function camel($input)
    {
        if (isset(static::$camelCache[$input])) {
            return static::$camelCache[$input];
        }

        return static::$camelCache[$input] = lcfirst(str_replace(' ', '', ucwords(strtr($input, '_-', '  '))));
    }
}
