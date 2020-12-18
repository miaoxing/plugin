<?php

namespace Miaoxing\Plugin\Model;

use Closure;
use Miaoxing\Plugin\Db\BaseDriver;

/**
 * @author Twin Huang <twinhuang@qq.com>
 * @mixin \DbMixin
 * @mixin \TagCacheMixin
 * @property \Wei\Cache $cache A cache service proxy 不引入 \CacheMixin 以免 phpstan 识别为 mixin 的 cache 方法
 * @property string $table The name of the table
 * @property array $columns The column names of the table
 *                          If leave it blank, it will automatic generate form the database table,
 *                          or fill it to speed up the record
 * @internal
 */
trait QueryBuilderTrait
{
    /**
     * A callback, use to convert the PHP array key name to a table or column name,
     * usually to convert from camel case to snake case
     *
     * @var callable|null
     */
    protected $dbKeyConverter = null;

    /**
     * A callback, used to convert the name of the table or column to the format required by PHP,
     * usually to convert from snake case to camel case
     *
     * @var callable|null
     */
    protected $phpKeyConverter = null;

    /**
     * The parts of query
     *
     * - indexBy  A field to be the key of the fetched array, if not provided, return default number index array
     *
     * @var array
     */
    protected $queryParts = [
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
        'indexBy' => null,
    ];

    /**
     * The query parameters
     *
     * @var array
     */
    protected $queryParams = [
        'set' => [],
        'where' => [],
    ];

    /**
     * The parameter type map of this query
     *
     * @var array
     */
    protected $queryParamTypes = [];

    /**
     * The type of query this is. Can be select, update or delete
     *
     * @var int
     */
    protected $queryType = BaseDriver::SELECT;

    /**
     * Indicates whether the query statement is changed, so the SQL must be regenerated.
     *
     * @var bool
     */
    protected $queryChanged = false;

    /**
     * @var string the complete SQL string for this query
     */
    protected $sql;

    /**
     * @var BaseDriver[]
     */
    protected static $dbDrivers = [];

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
     * @return static
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
     * Execute this query using the bound parameters and their types
     *
     * @return mixed
     */
    public function execute()
    {
        if (BaseDriver::SELECT == $this->queryType) {
            if ($this->hasCacheConfig()) {
                return $this->fetchFromCache();
            } else {
                return $this->executeFetchAll($this->getSql(), $this->getBindParams(), $this->queryParamTypes);
            }
        } else {
            return $this->db->executeUpdate($this->getSql(), $this->getBindParams(), $this->queryParamTypes);
        }
    }

    /**
     * Executes the generated query and returns a column value of the first row
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return string|null
     */
    public function fetchColumn($column = null, $operator = null, $value = null)
    {
        $data = $this->fetch(...func_get_args());
        return $data ? current($data) : null;
    }

    /**
     * Executes a sub query to receive the rows number
     *
     * @return int
     * @todo 改为自动识别
     */
    public function countBySubQuery()
    {
        return (int) $this->where(...func_get_args())->fetchColumn();
        //return (int) $this->db->fetchColumn($this->getSqlForCount(), $this->getBindParams());
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
     * Adds a restriction over the groups of the query, forming a logical
     * disjunction with any existing having restrictions.
     *
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
    public function getQueryPart($name)
    {
        return $this->queryParts[$name] ?? null;
    }

    /**
     * Get all SQL parts
     *
     * @return array
     */
    public function getQueryParts()
    {
        return $this->queryParts;
    }

    /**
     * Reset all SQL parts
     *
     * @param array $name
     * @return $this
     */
    public function resetQueryParts($name = null)
    {
        if (null === $name) {
            $name = array_keys($this->queryParts);
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
    public function setQueryParam($key, $value, $type = null)
    {
        if (null !== $type) {
            $this->queryParamTypes[$key] = $type;
        }

        $this->queryParams[$key] = $value;
        return $this;
    }

    /**
     * Gets a (previously set) query parameter of the query being constructed
     *
     * @param mixed $key The key (index or name) of the bound parameter
     * @return mixed The value of the bound parameter
     */
    public function getQueryParam($key)
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
    public function setQueryParams(array $params, array $types = [])
    {
        $this->queryParamTypes = $types;
        $this->queryParams = $params;
        return $this;
    }

    /**
     * Gets all defined query parameters for the query being constructed.
     *
     * @return array the currently defined query parameters
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * @param array|string $param
     * @param string $type
     * @return $this
     */
    public function addQueryParam($param, $type = 'where')
    {
        $this->queryParams[$type][] = (array) $param;
        return $this;
    }

    /**
     * @param string|null $type
     * @return $this
     */
    public function removeParam($type = null)
    {
        if ($type) {
            $this->queryParams[$type] = [];
        } else {
            foreach ($this->queryParams as $paramType => $params) {
                $this->queryParams[$paramType] = [];
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
        if (null !== $this->sql && !$this->queryChanged) {
            return $this->sql;
        }

        if (!$this->queryParts['from']) {
            $this->queryParts['from'] = $this->getTable();
        }

        $this->sql = $this->getDbDriver()->getSql($this->queryType, $this->queryParts, $this->dbKeyConverter);

        $this->queryChanged = false;

        return $this->sql;
    }

    public function getRawSql()
    {
        return $this->getDbDriver()->getRawSql(
            $this->queryType,
            $this->queryParts,
            $this->dbKeyConverter,
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
        foreach ($this->queryParams as $value) {
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
        $this->queryParams = [];
        $this->queryParamTypes = [];

        return $this->resetQueryParts();
    }

    /**
     * @return callable
     */
    public function getDbKeyConverter()
    {
        return $this->dbKeyConverter;
    }

    /**
     * Return the record table name
     *
     * @return string|null
     * @svc
     */
    protected function getTable()
    {
        return $this->table ?? null;
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @svc
     */
    protected function getColumns()
    {
        if (!isset($this->columns)) {
            $this->columns = array_map([$this, 'convertToPhpKey'], $this->db->getTableFields($this->getTable()));
        }
        return $this->columns;
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     */
    public function orWhereBetween($column, array $params)
    {
        return $this->addWhere($column, 'BETWEEN', $params, 'OR');
    }

    /**
     * Executes the generated query and returns the first array result
     *
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
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @svc
     */
    protected function fetchAll($column = null, $operator = null, $value = null)
    {
        $this->where(...func_get_args());
        $data = $this->execute();
        if ($this->queryParts['indexBy']) {
            $data = $this->executeIndexBy($data, $this->queryParts['indexBy']);
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
     * @return array
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
     * @param mixed $value
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
        $this->queryParams['set'][] = array_merge($this->queryParams['set'], $params);

        $this->queryType = BaseDriver::UPDATE;
        return $this->execute();
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @svc
     */
    protected function delete($column = null, $operator = null, $value = null)
    {
        $this->where(...func_get_args());
        $this->queryType = BaseDriver::DELETE;
        return $this->execute();
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
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
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @svc
     */
    protected function limit($limit)
    {
        $limit = max(1, (int) $limit);
        $this->add('limit', $limit);

        // 计算出新的offset
        if ($page = $this->getQueryPart('page')) {
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

        $limit = $this->getQueryPart('limit');
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
        $this->queryType = BaseDriver::SELECT;

        $columns = is_array($columns) ? $columns : func_get_args();

        return $this->add('select', (array) $columns, true);
    }

    /**
     * @param array|string $columns
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
        $this->queryType = BaseDriver::SELECT;

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
        $columns = array_diff($this->getColumns(), is_array($columns) ? $columns : [$columns]);

        return $this->select($columns);
    }

    /**
     * Specifies an item of the main table that is to be returned in the query result.
     * Default to all columns of the main table
     *
     * @param string $column
     * @return $this
     * @svc
     */
    protected function selectMain(string $column = '*'): self
    {
        return $this->select($this->getTable() . '.' . $column);
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
     * @param mixed|null $operator
     * @param mixed|null $value
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
     * @param mixed $params
     * @return $this
     * @svc
     */
    protected function whereRaw($expression, $params = [])
    {
        return $this->where($this->raw($expression), null, $params);
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @svc
     */
    protected function whereBetween($column, array $params)
    {
        return $this->addWhere($column, 'BETWEEN', $params);
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @svc
     */
    protected function whereNotBetween($column, array $params)
    {
        return $this->addWhere($column, 'NOT BETWEEN', $params);
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @svc
     */
    protected function whereIn($column, array $params)
    {
        return $this->addWhere($column, 'IN', $params);
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @svc
     */
    protected function whereNotIn($column, array $params)
    {
        return $this->addWhere($column, 'NOT IN', $params);
    }

    /**
     * @param string $column
     * @return $this
     * @svc
     */
    protected function whereNull($column)
    {
        return $this->addWhere($column, 'NULL');
    }

    /**
     * @param string $column
     * @return $this
     * @svc
     */
    protected function whereNotNULL($column)
    {
        return $this->addWhere($column, 'NOT NULL');
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @svc
     */
    protected function whereDate($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'DATE');
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @svc
     */
    protected function whereMonth($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'MONTH');
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @svc
     */
    protected function whereDay($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'DAY');
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @svc
     */
    protected function whereYear($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'YEAR');
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @svc
     */
    protected function whereTime($column, $opOrValue, $value = null)
    {
        return $this->addWhereArgs(func_get_args(), 'AND', 'TIME');
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
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
     * @param mixed $column
     * @param mixed $value
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
            $this->queryParams['having'][] = (array) $value;
        }

        $this->queryParts['having'][] = compact('column', 'operator', 'value', 'condition');

        return $this;
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @svc
     */
    public function havingRaw($expression, $params = [])
    {
        return $this->having($this->raw($expression), null, $params);
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
        $this->queryParts['indexBy'] = $column;
        return $this;
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
     * @param string|bool $lock
     * @return $this
     * @svc
     */
    protected function lock($lock)
    {
        $this->queryParts['lock'] = $lock;

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
     * @param callable|null $converter
     * @return $this
     * @svc
     */
    protected function setDbKeyConverter(callable $converter = null)
    {
        $this->dbKeyConverter = $converter;
        return $this;
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @svc
     */
    protected function setPhpKeyConverter(callable $converter = null)
    {
        $this->phpKeyConverter = $converter;
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
     */
    protected function resetSqlPart($name)
    {
        $this->queryParts[$name] = is_array($this->queryParts[$name]) ? [] : null;
        $this->queryChanged = true;
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
        $this->queryChanged = true;

        $isArray = is_array($sqlPart);
        $isMultiple = is_array($this->queryParts[$sqlPartName]);

        if ($isMultiple && !$isArray) {
            $sqlPart = [$sqlPart];
        }

        if ($append) {
            if ('orderBy' === $sqlPartName
                || 'groupBy' === $sqlPartName
                || 'select' === $sqlPartName
                || 'set' === $sqlPartName
            ) {
                $this->queryParts[$sqlPartName] = array_merge($this->queryParts[$sqlPartName], $sqlPart);
            } elseif ($isMultiple) {
                $this->queryParts[$sqlPartName][] = $sqlPart;
            }
            return $this;
        }

        $this->queryParts[$sqlPartName] = $sqlPart;
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
            $this->queryParams['where'] = array_merge($this->queryParams['where'], $query->getQueryParams()['where']);
        }

        if (null === $value) {
            $operator = 'NOT NULL' === $operator ? $operator : 'NULL';
        } elseif (is_array($value) && !in_array($operator, ['BETWEEN', 'NOT BETWEEN'], true)) {
            $operator = 'NOT IN' === $operator ? $operator : 'IN';
            $this->queryParams['where'][] = (array) $value;
        } else {
            $this->queryParams['where'][] = (array) $value;
        }

        $this->queryParts['where'][] = compact('column', 'operator', 'value', 'condition', 'type');

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
     * @param string $key
     * @return string
     */
    protected function convertToDbKey($key)
    {
        return isset($this->dbKeyConverter) ? call_user_func($this->dbKeyConverter, $key) : $key;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function convertToPhpKey($key)
    {
        return isset($this->phpKeyConverter) ? call_user_func($this->phpKeyConverter, $key) : $key;
    }

    /**
     * Convert db array keys to php keys
     *
     * @param array $data
     * @return array
     */
    protected function convertKeysToPhpKeys(array $data)
    {
        $newData = [];
        foreach ($data as $key => $value) {
            $newData[$this->convertToPhpKey($key)] = $value;
        }
        return $newData;
    }

    /**
     * Convert PHP array keys to db keys
     *
     * @param array $data
     * @return array
     */
    protected function convertKeysToDbKeys(array $data)
    {
        $newData = [];
        foreach ($data as $key => $value) {
            $newData[$this->convertToDbKey($key)] = $value;
        }
        return $newData;
    }

    /**
     * @return BaseDriver
     */
    protected function getDbDriver()
    {
        $driver = $this->db->getDriver();
        if (!isset(static::$dbDrivers[$driver])) {
            $class = 'Miaoxing\Plugin\Db\\' . ucfirst($driver);
            static::$dbDrivers[$driver] = new $class();
        }
        return static::$dbDrivers[$driver];
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

    /**
     * @return mixed
     */
    protected function fetchFromCache()
    {
        return $this->getCache()->get($this->getCacheKey(), $this->getCacheTime(), function () {
            return $this->executeFetchAll($this->getSql(), $this->getBindParams(), $this->queryParamTypes);
        });
    }

    /**
     * @param string $sql
     * @param array $params
     * @param array $types
     * @return array
     * @internal
     */
    protected function executeFetchAll($sql, $params = [], $types = [])
    {
        $data = $this->db->fetchAll($sql, $params, $types);
        if (isset($data[0])) {
            foreach ($data as &$row) {
                $row = $this->convertKeysToPhpKeys($row);
            }
            return $data;
        } else {
            return $this->convertKeysToPhpKeys($data);
        }
    }
}
