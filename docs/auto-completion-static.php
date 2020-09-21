<?php

namespace Miaoxing\Plugin\Service;

class App
{
    /**
     * 根据域名查找应用名称
     *
     * @param string $domain
     * @return false|string
     * @see App::getIdByDomain
     */
    public static function getIdByDomain($domain)
    {
    }
}

class AppModel
{
    /**
     * Returns the record data as array
     *
     * @param array|callable $returnFields A indexed array specified the fields to return
     * @param callable|null $prepend
     * @return array
     * @see Model::toArray
     */
    public static function toArray($returnFields = [], callable $prepend = null)
    {
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $data
     * @return $this
     * @see Model::saveData
     */
    public static function saveData($data = [])
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return array
     * @see Model::toRet
     */
    public static function toRet(array $merge = [])
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see Model::getTable
     */
    public static function getTable()
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     * @see Model::fromArray
     */
    public static function fromArray($data)
    {
    }

    /**
     * Save the record or data to database
     *
     * @param array $data
     * @return $this
     * @see Model::save
     */
    public static function save($data = [])
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see Model::destroy
     */
    public static function destroy($id = null)
    {
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see Model::set
     */
    public static function set($name, $value = null, $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see Model::find
     */
    public static function find($id)
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see Model::findOrFail
     */
    public static function findOrFail($id)
    {
    }

    /**
     * Find a record by primary key, or init with the specified data if record not found
     *
     * @param int|string $id
     * @param array|object $data
     * @return $this
     * @see Model::findOrInit
     */
    public static function findOrInit($id = null, $data = [])
    {
    }

    /**
     * Find a record by primary key, or save with the specified data if record not found
     *
     * @param int|string $id
     * @param array $data
     * @return $this
     * @see Model::findOrCreate
     */
    public static function findOrCreate($id, $data = [])
    {
    }

    /**
     * @param array $attributes
     * @param array $data
     * @return $this
     * @see Model::findByOrCreate
     */
    public static function findByOrCreate($attributes, $data = [])
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see Model::findAll
     */
    public static function findAll($ids)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see Model::findBy
     */
    public static function findBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see Model::findAllBy
     */
    public static function findAllBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see Model::findOrInitBy
     */
    public static function findOrInitBy($attributes, $data = [])
    {
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this
     * @throws \Exception
     * @see Model::findByOrFail
     */
    public static function findByOrFail($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @deprecated Use `findFromReq` instead
     * @see Model::findFromRequest
     */
    public static function findFromRequest($req = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @see Model::findFromReq
     */
    public static function findFromReq($req = null)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see Model::first
     */
    public static function first()
    {
    }

    /**
     * @return $this|array
     * @see Model::all
     */
    public static function all()
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see Model::indexBy
     */
    public static function indexBy($column)
    {
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @see QueryBuilder::getFields
     */
    public static function getFields()
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public static function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see QueryBuilder::fetchAll
     */
    public static function fetchAll($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public static function pluck(string $column, string $index = null)
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public static function chunk(int $count, callable $callback)
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public static function cnt($column = '*')
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see QueryBuilder::update
     */
    public static function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @see QueryBuilder::delete
     */
    public static function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public static function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public static function limit($limit)
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public static function page($page)
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see QueryBuilder::select
     */
    public static function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectDistinct
     */
    public static function selectDistinct($columns)
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public static function selectRaw($expression)
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectExcept
     */
    public static function selectExcept($columns)
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see QueryBuilder::from
     */
    public static function from($table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see QueryBuilder::table
     */
    public static function table(string $table, $alias = null): self
    {
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
     * @see QueryBuilder::join
     */
    public static function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER')
    {
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::innerJoin
     */
    public static function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::leftJoin
     */
    public static function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::rightJoin
     */
    public static function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
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
     * @see QueryBuilder::where
     */
    public static function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public static function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public static function whereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::orWhereBetween
     */
    public static function orWhereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public static function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public static function whereIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public static function whereNotIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public static function whereNull($column)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public static function whereNotNULL($column)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public static function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public static function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public static function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public static function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public static function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public static function whereColumn($column, $opOrColumn2, $column2 = null)
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public static function whereContains($column, $value, string $condition = 'AND')
    {
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public static function whereNotContains($column, $value, string $condition = 'OR')
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see QueryBuilder::groupBy
     */
    public static function groupBy($column)
    {
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
     * @see QueryBuilder::having
     */
    public static function having($column, $operator, $value = null, $condition = 'AND')
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see QueryBuilder::orderBy
     */
    public static function orderBy($column, $order = 'ASC')
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public static function desc($field)
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public static function asc($field)
    {
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @see QueryBuilder::resetSqlPart
     */
    public static function resetSqlPart($name)
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public static function forUpdate()
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public static function forShare()
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public static function lock($lock)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public static function when($value, $callback, callable $default = null)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public static function unless($value, callable $callback, callable $default = null)
    {
    }

    /**
     * @param callable $converter
     * @return $this
     * @see QueryBuilder::setInputIdentifierConverter
     */
    public static function setInputIdentifierConverter(callable $converter)
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param false|int|null $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public static function cache($seconds = null)
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see Model::unscoped
     */
    public static function unscoped($scopes = [])
    {
    }
}

class Config
{
    /**
     * @see Config::save
     * @param array|string $name
     * @param mixed $value
     * @return void
     */
    public static function save($name, $value = null)
    {
    }

    /**
     * @see Config::write
     */
    public static function write()
    {
    }

    /**
     * @see Config::load
     */
    public static function load()
    {
    }
}

class Jwt
{
    /**
     * @param array $claims
     * @param int $expire
     * @return Token
     * @throws \Exception
     * @see Jwt::generate
     */
    public static function generate(array $claims, int $expire = 2592000): \Lcobucci\JWT\Token
    {
    }

    /**
     * @param string $token
     * @return Ret
     * @see Jwt::verify
     */
    public static function verify(string $token): Ret
    {
    }

    /**
     * 生成默认配置所需的密钥
     *
     * @see Jwt::generateDefaultKeys
     * @experimental
     */
    public static function generateDefaultKeys(): Ret
    {
    }
}

class Model
{
    /**
     * Returns the record data as array
     *
     * @param array|callable $returnFields A indexed array specified the fields to return
     * @param callable|null $prepend
     * @return array
     * @see Model::toArray
     */
    public static function toArray($returnFields = [], callable $prepend = null)
    {
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $data
     * @return $this
     * @see Model::saveData
     */
    public static function saveData($data = [])
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return array
     * @see Model::toRet
     */
    public static function toRet(array $merge = [])
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see Model::getTable
     */
    public static function getTable()
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     * @see Model::fromArray
     */
    public static function fromArray($data)
    {
    }

    /**
     * Save the record or data to database
     *
     * @param array $data
     * @return $this
     * @see Model::save
     */
    public static function save($data = [])
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see Model::destroy
     */
    public static function destroy($id = null)
    {
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see Model::set
     */
    public static function set($name, $value = null, $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see Model::find
     */
    public static function find($id)
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see Model::findOrFail
     */
    public static function findOrFail($id)
    {
    }

    /**
     * Find a record by primary key, or init with the specified data if record not found
     *
     * @param int|string $id
     * @param array|object $data
     * @return $this
     * @see Model::findOrInit
     */
    public static function findOrInit($id = null, $data = [])
    {
    }

    /**
     * Find a record by primary key, or save with the specified data if record not found
     *
     * @param int|string $id
     * @param array $data
     * @return $this
     * @see Model::findOrCreate
     */
    public static function findOrCreate($id, $data = [])
    {
    }

    /**
     * @param array $attributes
     * @param array $data
     * @return $this
     * @see Model::findByOrCreate
     */
    public static function findByOrCreate($attributes, $data = [])
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see Model::findAll
     */
    public static function findAll($ids)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see Model::findBy
     */
    public static function findBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see Model::findAllBy
     */
    public static function findAllBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see Model::findOrInitBy
     */
    public static function findOrInitBy($attributes, $data = [])
    {
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this
     * @throws \Exception
     * @see Model::findByOrFail
     */
    public static function findByOrFail($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @deprecated Use `findFromReq` instead
     * @see Model::findFromRequest
     */
    public static function findFromRequest($req = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @see Model::findFromReq
     */
    public static function findFromReq($req = null)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see Model::first
     */
    public static function first()
    {
    }

    /**
     * @return $this|array
     * @see Model::all
     */
    public static function all()
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see Model::indexBy
     */
    public static function indexBy($column)
    {
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @see QueryBuilder::getFields
     */
    public static function getFields()
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public static function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see QueryBuilder::fetchAll
     */
    public static function fetchAll($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public static function pluck(string $column, string $index = null)
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public static function chunk(int $count, callable $callback)
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public static function cnt($column = '*')
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see QueryBuilder::update
     */
    public static function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @see QueryBuilder::delete
     */
    public static function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public static function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public static function limit($limit)
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public static function page($page)
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see QueryBuilder::select
     */
    public static function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectDistinct
     */
    public static function selectDistinct($columns)
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public static function selectRaw($expression)
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectExcept
     */
    public static function selectExcept($columns)
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see QueryBuilder::from
     */
    public static function from($table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see QueryBuilder::table
     */
    public static function table(string $table, $alias = null): self
    {
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
     * @see QueryBuilder::join
     */
    public static function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER')
    {
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::innerJoin
     */
    public static function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::leftJoin
     */
    public static function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::rightJoin
     */
    public static function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
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
     * @see QueryBuilder::where
     */
    public static function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public static function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public static function whereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::orWhereBetween
     */
    public static function orWhereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public static function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public static function whereIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public static function whereNotIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public static function whereNull($column)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public static function whereNotNULL($column)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public static function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public static function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public static function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public static function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public static function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public static function whereColumn($column, $opOrColumn2, $column2 = null)
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public static function whereContains($column, $value, string $condition = 'AND')
    {
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public static function whereNotContains($column, $value, string $condition = 'OR')
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see QueryBuilder::groupBy
     */
    public static function groupBy($column)
    {
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
     * @see QueryBuilder::having
     */
    public static function having($column, $operator, $value = null, $condition = 'AND')
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see QueryBuilder::orderBy
     */
    public static function orderBy($column, $order = 'ASC')
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public static function desc($field)
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public static function asc($field)
    {
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @see QueryBuilder::resetSqlPart
     */
    public static function resetSqlPart($name)
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public static function forUpdate()
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public static function forShare()
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public static function lock($lock)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public static function when($value, $callback, callable $default = null)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public static function unless($value, callable $callback, callable $default = null)
    {
    }

    /**
     * @param callable $converter
     * @return $this
     * @see QueryBuilder::setInputIdentifierConverter
     */
    public static function setInputIdentifierConverter(callable $converter)
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param false|int|null $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public static function cache($seconds = null)
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see Model::unscoped
     */
    public static function unscoped($scopes = [])
    {
    }
}

class PageRouter
{
}

class Plugin
{
    /**
     * Check if a plugin exists
     *
     * @param string $id
     * @return bool
     * @see Plugin::has
     */
    public static function has($id)
    {
    }

    /**
     * Check if a plugin is installed
     *
     * @param string $id
     * @return bool
     * @see Plugin::isInstalled
     */
    public static function isInstalled($id)
    {
    }
}

class QueryBuilder
{
    /**
     * Return the record table name
     *
     * @return string
     * @see QueryBuilder::getTable
     */
    public static function getTable()
    {
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @see QueryBuilder::getFields
     */
    public static function getFields()
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public static function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see QueryBuilder::fetchAll
     */
    public static function fetchAll($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return array|null
     * @see QueryBuilder::first
     */
    public static function first()
    {
    }

    /**
     * @return array
     * @see QueryBuilder::all
     */
    public static function all()
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public static function pluck(string $column, string $index = null)
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public static function chunk(int $count, callable $callback)
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public static function cnt($column = '*')
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see QueryBuilder::update
     */
    public static function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @see QueryBuilder::delete
     */
    public static function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public static function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public static function limit($limit)
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public static function page($page)
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see QueryBuilder::select
     */
    public static function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectDistinct
     */
    public static function selectDistinct($columns)
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public static function selectRaw($expression)
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectExcept
     */
    public static function selectExcept($columns)
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see QueryBuilder::from
     */
    public static function from($table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see QueryBuilder::table
     */
    public static function table(string $table, $alias = null): self
    {
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
     * @see QueryBuilder::join
     */
    public static function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER')
    {
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::innerJoin
     */
    public static function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::leftJoin
     */
    public static function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::rightJoin
     */
    public static function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
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
     * @see QueryBuilder::where
     */
    public static function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public static function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public static function whereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::orWhereBetween
     */
    public static function orWhereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public static function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public static function whereIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public static function whereNotIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public static function whereNull($column)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public static function whereNotNULL($column)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public static function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public static function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public static function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public static function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public static function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public static function whereColumn($column, $opOrColumn2, $column2 = null)
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public static function whereContains($column, $value, string $condition = 'AND')
    {
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public static function whereNotContains($column, $value, string $condition = 'OR')
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see QueryBuilder::groupBy
     */
    public static function groupBy($column)
    {
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
     * @see QueryBuilder::having
     */
    public static function having($column, $operator, $value = null, $condition = 'AND')
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see QueryBuilder::orderBy
     */
    public static function orderBy($column, $order = 'ASC')
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public static function desc($field)
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public static function asc($field)
    {
    }

    /**
     * Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @see QueryBuilder::indexBy
     */
    public static function indexBy($column)
    {
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @see QueryBuilder::resetSqlPart
     */
    public static function resetSqlPart($name)
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public static function forUpdate()
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public static function forShare()
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public static function lock($lock)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public static function when($value, $callback, callable $default = null)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public static function unless($value, callable $callback, callable $default = null)
    {
    }

    /**
     * @param callable $converter
     * @return $this
     * @see QueryBuilder::setInputIdentifierConverter
     */
    public static function setInputIdentifierConverter(callable $converter)
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param false|int|null $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public static function cache($seconds = null)
    {
    }
}

class Ret
{
    /**
     * Return operation successful result
     *
     * ```php
     * // Specified message
     * $this->suc('Payment successful');
     *
     * // Format
     * $this->suc(['me%sage', 'ss']);
     *
     * // More data
     * $this->suc(['message' => 'Read successful', 'page' => 1, 'rows' => 123]);
     * ```
     *
     * @param array|string|null $message
     * @return $this
     * @see Ret::suc
     */
    public static function suc($message = null)
    {
    }

    /**
     * Return operation failed result, and logs with an info level
     *
     * @param array|string $message
     * @param int $code
     * @param string $level
     * @return $this
     * @see Ret::err
     */
    public static function err($message, $code = 0, $level = 'info')
    {
    }
}

class Schema
{
}

class Session
{
}

class Str
{
}

class Tester
{
    /**
     * @param array $request
     * @return $this
     * @see Tester::request
     */
    public static function request(array $request)
    {
    }

    /**
     * @param string $page
     * @return mixed
     * @see Tester::patch
     */
    public static function patch(string $page)
    {
    }

    /**
     * @param string $page
     * @return mixed
     * @see Tester::delete
     */
    public static function delete(string $page)
    {
    }

    /**
     * @param string $page
     * @return mixed
     * @see Tester::getAdminApi
     */
    public static function getAdminApi(string $page)
    {
    }

    /**
     * @param string $page
     * @param array $data
     * @return mixed
     * @see Tester::postAdminApi
     */
    public static function postAdminApi(string $page, $data = [])
    {
    }

    /**
     * @param string $page
     * @param array $data
     * @return mixed
     * @see Tester::patchAdminApi
     */
    public static function patchAdminApi(string $page, $data = [])
    {
    }

    /**
     * @param string $page
     * @return mixed
     * @see Tester::deleteAdminApi
     */
    public static function deleteAdminApi(string $page)
    {
    }
}

class User
{
    /**
     * @return int|null
     * @see User::id
     */
    public static function id()
    {
    }

    /**
     * @return UserModel
     * @see User::cur
     */
    public static function cur()
    {
    }

    /**
     * 判断用户是否登录
     *
     * @return bool
     * @see User::isLogin
     */
    public static function isLogin()
    {
    }

    /**
     * 检查用户是否登录
     *
     * @return Ret
     * @see User::checkLogin
     */
    public static function checkLogin()
    {
    }

    /**
     * 根据用户账号密码,登录用户
     *
     * @param mixed $data
     * @return Ret
     * @see User::login
     */
    public static function login($data)
    {
    }

    /**
     * 根据用户ID直接登录用户
     *
     * @param int $id
     * @return Ret
     * @see User::loginById
     */
    public static function loginById($id)
    {
    }

    /**
     * 根据条件查找或创建用户,并登录
     *
     * @param mixed $conditions
     * @param array $data
     * @return $this
     * @see User::loginBy
     */
    public static function loginBy($conditions, $data = [])
    {
    }

    /**
     * 根据用户对象登录用户
     *
     * @param UserModel $user
     * @return Ret
     * @see User::loginByModel
     */
    public static function loginByModel(UserModel $user)
    {
    }

    /**
     * 销毁用户会话,退出登录
     *
     * @return Ret
     * @see User::logout
     */
    public static function logout()
    {
    }

    /**
     * 当用户信息更改后,可以主动调用该方法,刷新会话中的数据
     *
     * @param UserModel $user
     * @return $this
     * @see User::refresh
     */
    public static function refresh(UserModel $user)
    {
    }

    /**
     * 通过外部检查用户是否有某个权限
     *
     * @param string $permissionId
     * @return bool
     * @see UserModel::can
     */
    public static function can($permissionId)
    {
    }

    /**
     * @param array|\ArrayAccess $req
     * @return Ret
     * @see UserModel::updatePassword
     */
    public static function updatePassword($req)
    {
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $data
     * @return $this
     * @see Model::saveData
     */
    public static function saveData($data = [])
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return array
     * @see Model::toRet
     */
    public static function toRet(array $merge = [])
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see Model::getTable
     */
    public static function getTable()
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     * @see Model::fromArray
     */
    public static function fromArray($data)
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see Model::destroy
     */
    public static function destroy($id = null)
    {
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see Model::set
     */
    public static function set($name, $value = null, $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see Model::find
     */
    public static function find($id)
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see Model::findOrFail
     */
    public static function findOrFail($id)
    {
    }

    /**
     * Find a record by primary key, or init with the specified data if record not found
     *
     * @param int|string $id
     * @param array|object $data
     * @return $this
     * @see Model::findOrInit
     */
    public static function findOrInit($id = null, $data = [])
    {
    }

    /**
     * Find a record by primary key, or save with the specified data if record not found
     *
     * @param int|string $id
     * @param array $data
     * @return $this
     * @see Model::findOrCreate
     */
    public static function findOrCreate($id, $data = [])
    {
    }

    /**
     * @param array $attributes
     * @param array $data
     * @return $this
     * @see Model::findByOrCreate
     */
    public static function findByOrCreate($attributes, $data = [])
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see Model::findAll
     */
    public static function findAll($ids)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see Model::findBy
     */
    public static function findBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see Model::findAllBy
     */
    public static function findAllBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see Model::findOrInitBy
     */
    public static function findOrInitBy($attributes, $data = [])
    {
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this
     * @throws \Exception
     * @see Model::findByOrFail
     */
    public static function findByOrFail($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @deprecated Use `findFromReq` instead
     * @see Model::findFromRequest
     */
    public static function findFromRequest($req = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @see Model::findFromReq
     */
    public static function findFromReq($req = null)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see Model::first
     */
    public static function first()
    {
    }

    /**
     * @return $this|array
     * @see Model::all
     */
    public static function all()
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see Model::indexBy
     */
    public static function indexBy($column)
    {
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @see QueryBuilder::getFields
     */
    public static function getFields()
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public static function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see QueryBuilder::fetchAll
     */
    public static function fetchAll($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public static function pluck(string $column, string $index = null)
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public static function chunk(int $count, callable $callback)
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public static function cnt($column = '*')
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see QueryBuilder::update
     */
    public static function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @see QueryBuilder::delete
     */
    public static function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public static function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public static function limit($limit)
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public static function page($page)
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see QueryBuilder::select
     */
    public static function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectDistinct
     */
    public static function selectDistinct($columns)
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public static function selectRaw($expression)
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectExcept
     */
    public static function selectExcept($columns)
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see QueryBuilder::from
     */
    public static function from($table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see QueryBuilder::table
     */
    public static function table(string $table, $alias = null): self
    {
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
     * @see QueryBuilder::join
     */
    public static function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER')
    {
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::innerJoin
     */
    public static function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::leftJoin
     */
    public static function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::rightJoin
     */
    public static function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
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
     * @see QueryBuilder::where
     */
    public static function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public static function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public static function whereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::orWhereBetween
     */
    public static function orWhereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public static function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public static function whereIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public static function whereNotIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public static function whereNull($column)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public static function whereNotNULL($column)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public static function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public static function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public static function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public static function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public static function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public static function whereColumn($column, $opOrColumn2, $column2 = null)
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public static function whereContains($column, $value, string $condition = 'AND')
    {
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public static function whereNotContains($column, $value, string $condition = 'OR')
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see QueryBuilder::groupBy
     */
    public static function groupBy($column)
    {
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
     * @see QueryBuilder::having
     */
    public static function having($column, $operator, $value = null, $condition = 'AND')
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see QueryBuilder::orderBy
     */
    public static function orderBy($column, $order = 'ASC')
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public static function desc($field)
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public static function asc($field)
    {
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @see QueryBuilder::resetSqlPart
     */
    public static function resetSqlPart($name)
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public static function forUpdate()
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public static function forShare()
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public static function lock($lock)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public static function when($value, $callback, callable $default = null)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public static function unless($value, callable $callback, callable $default = null)
    {
    }

    /**
     * @param callable $converter
     * @return $this
     * @see QueryBuilder::setInputIdentifierConverter
     */
    public static function setInputIdentifierConverter(callable $converter)
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param false|int|null $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public static function cache($seconds = null)
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see Model::unscoped
     */
    public static function unscoped($scopes = [])
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::like
     */
    public static function like($columns)
    {
    }
}

class UserModel
{
    /**
     * 通过外部检查用户是否有某个权限
     *
     * @param string $permissionId
     * @return bool
     * @see UserModel::can
     */
    public static function can($permissionId)
    {
    }

    /**
     * @param array|\ArrayAccess $req
     * @return Ret
     * @see UserModel::updatePassword
     */
    public static function updatePassword($req)
    {
    }

    /**
     * Returns the record data as array
     *
     * @param array|callable $returnFields A indexed array specified the fields to return
     * @param callable|null $prepend
     * @return array
     * @see Model::toArray
     */
    public static function toArray($returnFields = [], callable $prepend = null)
    {
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $data
     * @return $this
     * @see Model::saveData
     */
    public static function saveData($data = [])
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return array
     * @see Model::toRet
     */
    public static function toRet(array $merge = [])
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see Model::getTable
     */
    public static function getTable()
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     * @see Model::fromArray
     */
    public static function fromArray($data)
    {
    }

    /**
     * Save the record or data to database
     *
     * @param array $data
     * @return $this
     * @see Model::save
     */
    public static function save($data = [])
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see Model::destroy
     */
    public static function destroy($id = null)
    {
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see Model::set
     */
    public static function set($name, $value = null, $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see Model::find
     */
    public static function find($id)
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see Model::findOrFail
     */
    public static function findOrFail($id)
    {
    }

    /**
     * Find a record by primary key, or init with the specified data if record not found
     *
     * @param int|string $id
     * @param array|object $data
     * @return $this
     * @see Model::findOrInit
     */
    public static function findOrInit($id = null, $data = [])
    {
    }

    /**
     * Find a record by primary key, or save with the specified data if record not found
     *
     * @param int|string $id
     * @param array $data
     * @return $this
     * @see Model::findOrCreate
     */
    public static function findOrCreate($id, $data = [])
    {
    }

    /**
     * @param array $attributes
     * @param array $data
     * @return $this
     * @see Model::findByOrCreate
     */
    public static function findByOrCreate($attributes, $data = [])
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see Model::findAll
     */
    public static function findAll($ids)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see Model::findBy
     */
    public static function findBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see Model::findAllBy
     */
    public static function findAllBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see Model::findOrInitBy
     */
    public static function findOrInitBy($attributes, $data = [])
    {
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this
     * @throws \Exception
     * @see Model::findByOrFail
     */
    public static function findByOrFail($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @deprecated Use `findFromReq` instead
     * @see Model::findFromRequest
     */
    public static function findFromRequest($req = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @see Model::findFromReq
     */
    public static function findFromReq($req = null)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see Model::first
     */
    public static function first()
    {
    }

    /**
     * @return $this|array
     * @see Model::all
     */
    public static function all()
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see Model::indexBy
     */
    public static function indexBy($column)
    {
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @see QueryBuilder::getFields
     */
    public static function getFields()
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public static function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see QueryBuilder::fetchAll
     */
    public static function fetchAll($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public static function pluck(string $column, string $index = null)
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public static function chunk(int $count, callable $callback)
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public static function cnt($column = '*')
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see QueryBuilder::update
     */
    public static function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @see QueryBuilder::delete
     */
    public static function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public static function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public static function limit($limit)
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public static function page($page)
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see QueryBuilder::select
     */
    public static function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectDistinct
     */
    public static function selectDistinct($columns)
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public static function selectRaw($expression)
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectExcept
     */
    public static function selectExcept($columns)
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see QueryBuilder::from
     */
    public static function from($table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see QueryBuilder::table
     */
    public static function table(string $table, $alias = null): self
    {
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
     * @see QueryBuilder::join
     */
    public static function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER')
    {
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::innerJoin
     */
    public static function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::leftJoin
     */
    public static function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::rightJoin
     */
    public static function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
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
     * @see QueryBuilder::where
     */
    public static function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public static function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public static function whereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::orWhereBetween
     */
    public static function orWhereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public static function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public static function whereIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public static function whereNotIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public static function whereNull($column)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public static function whereNotNULL($column)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public static function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public static function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public static function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public static function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public static function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public static function whereColumn($column, $opOrColumn2, $column2 = null)
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public static function whereContains($column, $value, string $condition = 'AND')
    {
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public static function whereNotContains($column, $value, string $condition = 'OR')
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see QueryBuilder::groupBy
     */
    public static function groupBy($column)
    {
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
     * @see QueryBuilder::having
     */
    public static function having($column, $operator, $value = null, $condition = 'AND')
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see QueryBuilder::orderBy
     */
    public static function orderBy($column, $order = 'ASC')
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public static function desc($field)
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public static function asc($field)
    {
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @see QueryBuilder::resetSqlPart
     */
    public static function resetSqlPart($name)
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public static function forUpdate()
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public static function forShare()
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public static function lock($lock)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public static function when($value, $callback, callable $default = null)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public static function unless($value, callable $callback, callable $default = null)
    {
    }

    /**
     * @param callable $converter
     * @return $this
     * @see QueryBuilder::setInputIdentifierConverter
     */
    public static function setInputIdentifierConverter(callable $converter)
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param false|int|null $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public static function cache($seconds = null)
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see Model::unscoped
     */
    public static function unscoped($scopes = [])
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::like
     */
    public static function like($columns)
    {
    }
}

namespace Miaoxing\Plugin\Service;

if (0) {
class App
{
    /**
     * 根据域名查找应用名称
     *
     * @param string $domain
     * @return false|string
     * @see App::getIdByDomain
     */
    public function getIdByDomain($domain)
    {
    }
}

class AppModel
{
    /**
     * Returns the record data as array
     *
     * @param array|callable $returnFields A indexed array specified the fields to return
     * @param callable|null $prepend
     * @return array
     * @see Model::toArray
     */
    public function toArray($returnFields = [], callable $prepend = null)
    {
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $data
     * @return $this
     * @see Model::saveData
     */
    public function saveData($data = [])
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return array
     * @see Model::toRet
     */
    public function toRet(array $merge = [])
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see Model::getTable
     */
    public function getTable()
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     * @see Model::fromArray
     */
    public function fromArray($data)
    {
    }

    /**
     * Save the record or data to database
     *
     * @param array $data
     * @return $this
     * @see Model::save
     */
    public function save($data = [])
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see Model::destroy
     */
    public function destroy($id = null)
    {
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see Model::set
     */
    public function set($name, $value = null, $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see Model::find
     */
    public function find($id)
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see Model::findOrFail
     */
    public function findOrFail($id)
    {
    }

    /**
     * Find a record by primary key, or init with the specified data if record not found
     *
     * @param int|string $id
     * @param array|object $data
     * @return $this
     * @see Model::findOrInit
     */
    public function findOrInit($id = null, $data = [])
    {
    }

    /**
     * Find a record by primary key, or save with the specified data if record not found
     *
     * @param int|string $id
     * @param array $data
     * @return $this
     * @see Model::findOrCreate
     */
    public function findOrCreate($id, $data = [])
    {
    }

    /**
     * @param array $attributes
     * @param array $data
     * @return $this
     * @see Model::findByOrCreate
     */
    public function findByOrCreate($attributes, $data = [])
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see Model::findAll
     */
    public function findAll($ids)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see Model::findBy
     */
    public function findBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see Model::findAllBy
     */
    public function findAllBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see Model::findOrInitBy
     */
    public function findOrInitBy($attributes, $data = [])
    {
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this
     * @throws \Exception
     * @see Model::findByOrFail
     */
    public function findByOrFail($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @deprecated Use `findFromReq` instead
     * @see Model::findFromRequest
     */
    public function findFromRequest($req = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @see Model::findFromReq
     */
    public function findFromReq($req = null)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see Model::first
     */
    public function first()
    {
    }

    /**
     * @return $this|array
     * @see Model::all
     */
    public function all()
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see Model::indexBy
     */
    public function indexBy($column)
    {
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @see QueryBuilder::getFields
     */
    public function getFields()
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see QueryBuilder::fetchAll
     */
    public function fetchAll($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public function pluck(string $column, string $index = null)
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public function chunk(int $count, callable $callback)
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public function cnt($column = '*')
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see QueryBuilder::update
     */
    public function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @see QueryBuilder::delete
     */
    public function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public function limit($limit)
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public function page($page)
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see QueryBuilder::select
     */
    public function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectDistinct
     */
    public function selectDistinct($columns)
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public function selectRaw($expression)
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectExcept
     */
    public function selectExcept($columns)
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see QueryBuilder::from
     */
    public function from($table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see QueryBuilder::table
     */
    public function table(string $table, $alias = null): self
    {
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
     * @see QueryBuilder::join
     */
    public function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER')
    {
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::innerJoin
     */
    public function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::leftJoin
     */
    public function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::rightJoin
     */
    public function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
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
     * @see QueryBuilder::where
     */
    public function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public function whereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::orWhereBetween
     */
    public function orWhereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public function whereIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public function whereNotIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public function whereNull($column)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public function whereNotNULL($column)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public function whereColumn($column, $opOrColumn2, $column2 = null)
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public function whereContains($column, $value, string $condition = 'AND')
    {
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public function whereNotContains($column, $value, string $condition = 'OR')
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see QueryBuilder::groupBy
     */
    public function groupBy($column)
    {
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
     * @see QueryBuilder::having
     */
    public function having($column, $operator, $value = null, $condition = 'AND')
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see QueryBuilder::orderBy
     */
    public function orderBy($column, $order = 'ASC')
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public function desc($field)
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public function asc($field)
    {
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @see QueryBuilder::resetSqlPart
     */
    public function resetSqlPart($name)
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public function forUpdate()
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public function forShare()
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public function lock($lock)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public function when($value, $callback, callable $default = null)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public function unless($value, callable $callback, callable $default = null)
    {
    }

    /**
     * @param callable $converter
     * @return $this
     * @see QueryBuilder::setInputIdentifierConverter
     */
    public function setInputIdentifierConverter(callable $converter)
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param false|int|null $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public function cache($seconds = null)
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see Model::unscoped
     */
    public function unscoped($scopes = [])
    {
    }
}

class Config
{
    /**
     * @see Config::save
     * @param array|string $name
     * @param mixed $value
     * @return void
     */
    public function save($name, $value = null)
    {
    }

    /**
     * @see Config::write
     */
    public function write()
    {
    }

    /**
     * @see Config::load
     */
    public function load()
    {
    }
}

class Jwt
{
    /**
     * @param array $claims
     * @param int $expire
     * @return Token
     * @throws \Exception
     * @see Jwt::generate
     */
    public function generate(array $claims, int $expire = 2592000): \Lcobucci\JWT\Token
    {
    }

    /**
     * @param string $token
     * @return Ret
     * @see Jwt::verify
     */
    public function verify(string $token): Ret
    {
    }

    /**
     * 生成默认配置所需的密钥
     *
     * @see Jwt::generateDefaultKeys
     * @experimental
     */
    public function generateDefaultKeys(): Ret
    {
    }
}

class Model
{
    /**
     * Returns the record data as array
     *
     * @param array|callable $returnFields A indexed array specified the fields to return
     * @param callable|null $prepend
     * @return array
     * @see Model::toArray
     */
    public function toArray($returnFields = [], callable $prepend = null)
    {
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $data
     * @return $this
     * @see Model::saveData
     */
    public function saveData($data = [])
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return array
     * @see Model::toRet
     */
    public function toRet(array $merge = [])
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see Model::getTable
     */
    public function getTable()
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     * @see Model::fromArray
     */
    public function fromArray($data)
    {
    }

    /**
     * Save the record or data to database
     *
     * @param array $data
     * @return $this
     * @see Model::save
     */
    public function save($data = [])
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see Model::destroy
     */
    public function destroy($id = null)
    {
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see Model::set
     */
    public function set($name, $value = null, $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see Model::find
     */
    public function find($id)
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see Model::findOrFail
     */
    public function findOrFail($id)
    {
    }

    /**
     * Find a record by primary key, or init with the specified data if record not found
     *
     * @param int|string $id
     * @param array|object $data
     * @return $this
     * @see Model::findOrInit
     */
    public function findOrInit($id = null, $data = [])
    {
    }

    /**
     * Find a record by primary key, or save with the specified data if record not found
     *
     * @param int|string $id
     * @param array $data
     * @return $this
     * @see Model::findOrCreate
     */
    public function findOrCreate($id, $data = [])
    {
    }

    /**
     * @param array $attributes
     * @param array $data
     * @return $this
     * @see Model::findByOrCreate
     */
    public function findByOrCreate($attributes, $data = [])
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see Model::findAll
     */
    public function findAll($ids)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see Model::findBy
     */
    public function findBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see Model::findAllBy
     */
    public function findAllBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see Model::findOrInitBy
     */
    public function findOrInitBy($attributes, $data = [])
    {
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this
     * @throws \Exception
     * @see Model::findByOrFail
     */
    public function findByOrFail($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @deprecated Use `findFromReq` instead
     * @see Model::findFromRequest
     */
    public function findFromRequest($req = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @see Model::findFromReq
     */
    public function findFromReq($req = null)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see Model::first
     */
    public function first()
    {
    }

    /**
     * @return $this|array
     * @see Model::all
     */
    public function all()
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see Model::indexBy
     */
    public function indexBy($column)
    {
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @see QueryBuilder::getFields
     */
    public function getFields()
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see QueryBuilder::fetchAll
     */
    public function fetchAll($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public function pluck(string $column, string $index = null)
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public function chunk(int $count, callable $callback)
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public function cnt($column = '*')
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see QueryBuilder::update
     */
    public function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @see QueryBuilder::delete
     */
    public function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public function limit($limit)
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public function page($page)
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see QueryBuilder::select
     */
    public function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectDistinct
     */
    public function selectDistinct($columns)
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public function selectRaw($expression)
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectExcept
     */
    public function selectExcept($columns)
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see QueryBuilder::from
     */
    public function from($table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see QueryBuilder::table
     */
    public function table(string $table, $alias = null): self
    {
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
     * @see QueryBuilder::join
     */
    public function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER')
    {
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::innerJoin
     */
    public function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::leftJoin
     */
    public function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::rightJoin
     */
    public function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
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
     * @see QueryBuilder::where
     */
    public function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public function whereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::orWhereBetween
     */
    public function orWhereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public function whereIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public function whereNotIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public function whereNull($column)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public function whereNotNULL($column)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public function whereColumn($column, $opOrColumn2, $column2 = null)
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public function whereContains($column, $value, string $condition = 'AND')
    {
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public function whereNotContains($column, $value, string $condition = 'OR')
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see QueryBuilder::groupBy
     */
    public function groupBy($column)
    {
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
     * @see QueryBuilder::having
     */
    public function having($column, $operator, $value = null, $condition = 'AND')
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see QueryBuilder::orderBy
     */
    public function orderBy($column, $order = 'ASC')
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public function desc($field)
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public function asc($field)
    {
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @see QueryBuilder::resetSqlPart
     */
    public function resetSqlPart($name)
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public function forUpdate()
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public function forShare()
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public function lock($lock)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public function when($value, $callback, callable $default = null)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public function unless($value, callable $callback, callable $default = null)
    {
    }

    /**
     * @param callable $converter
     * @return $this
     * @see QueryBuilder::setInputIdentifierConverter
     */
    public function setInputIdentifierConverter(callable $converter)
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param false|int|null $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public function cache($seconds = null)
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see Model::unscoped
     */
    public function unscoped($scopes = [])
    {
    }
}

class PageRouter
{
}

class Plugin
{
    /**
     * Check if a plugin exists
     *
     * @param string $id
     * @return bool
     * @see Plugin::has
     */
    public function has($id)
    {
    }

    /**
     * Check if a plugin is installed
     *
     * @param string $id
     * @return bool
     * @see Plugin::isInstalled
     */
    public function isInstalled($id)
    {
    }
}

class QueryBuilder
{
    /**
     * Return the record table name
     *
     * @return string
     * @see QueryBuilder::getTable
     */
    public function getTable()
    {
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @see QueryBuilder::getFields
     */
    public function getFields()
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see QueryBuilder::fetchAll
     */
    public function fetchAll($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return array|null
     * @see QueryBuilder::first
     */
    public function first()
    {
    }

    /**
     * @return array
     * @see QueryBuilder::all
     */
    public function all()
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public function pluck(string $column, string $index = null)
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public function chunk(int $count, callable $callback)
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public function cnt($column = '*')
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see QueryBuilder::update
     */
    public function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @see QueryBuilder::delete
     */
    public function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public function limit($limit)
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public function page($page)
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see QueryBuilder::select
     */
    public function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectDistinct
     */
    public function selectDistinct($columns)
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public function selectRaw($expression)
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectExcept
     */
    public function selectExcept($columns)
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see QueryBuilder::from
     */
    public function from($table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see QueryBuilder::table
     */
    public function table(string $table, $alias = null): self
    {
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
     * @see QueryBuilder::join
     */
    public function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER')
    {
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::innerJoin
     */
    public function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::leftJoin
     */
    public function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::rightJoin
     */
    public function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
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
     * @see QueryBuilder::where
     */
    public function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public function whereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::orWhereBetween
     */
    public function orWhereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public function whereIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public function whereNotIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public function whereNull($column)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public function whereNotNULL($column)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public function whereColumn($column, $opOrColumn2, $column2 = null)
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public function whereContains($column, $value, string $condition = 'AND')
    {
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public function whereNotContains($column, $value, string $condition = 'OR')
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see QueryBuilder::groupBy
     */
    public function groupBy($column)
    {
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
     * @see QueryBuilder::having
     */
    public function having($column, $operator, $value = null, $condition = 'AND')
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see QueryBuilder::orderBy
     */
    public function orderBy($column, $order = 'ASC')
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public function desc($field)
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public function asc($field)
    {
    }

    /**
     * Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @see QueryBuilder::indexBy
     */
    public function indexBy($column)
    {
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @see QueryBuilder::resetSqlPart
     */
    public function resetSqlPart($name)
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public function forUpdate()
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public function forShare()
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public function lock($lock)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public function when($value, $callback, callable $default = null)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public function unless($value, callable $callback, callable $default = null)
    {
    }

    /**
     * @param callable $converter
     * @return $this
     * @see QueryBuilder::setInputIdentifierConverter
     */
    public function setInputIdentifierConverter(callable $converter)
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param false|int|null $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public function cache($seconds = null)
    {
    }
}

class Ret
{
    /**
     * Return operation successful result
     *
     * ```php
     * // Specified message
     * $this->suc('Payment successful');
     *
     * // Format
     * $this->suc(['me%sage', 'ss']);
     *
     * // More data
     * $this->suc(['message' => 'Read successful', 'page' => 1, 'rows' => 123]);
     * ```
     *
     * @param array|string|null $message
     * @return $this
     * @see Ret::suc
     */
    public function suc($message = null)
    {
    }

    /**
     * Return operation failed result, and logs with an info level
     *
     * @param array|string $message
     * @param int $code
     * @param string $level
     * @return $this
     * @see Ret::err
     */
    public function err($message, $code = 0, $level = 'info')
    {
    }
}

class Schema
{
}

class Session
{
}

class Str
{
}

class Tester
{
    /**
     * @param array $request
     * @return $this
     * @see Tester::request
     */
    public function request(array $request)
    {
    }

    /**
     * @param string $page
     * @return mixed
     * @see Tester::patch
     */
    public function patch(string $page)
    {
    }

    /**
     * @param string $page
     * @return mixed
     * @see Tester::delete
     */
    public function delete(string $page)
    {
    }

    /**
     * @param string $page
     * @return mixed
     * @see Tester::getAdminApi
     */
    public function getAdminApi(string $page)
    {
    }

    /**
     * @param string $page
     * @param array $data
     * @return mixed
     * @see Tester::postAdminApi
     */
    public function postAdminApi(string $page, $data = [])
    {
    }

    /**
     * @param string $page
     * @param array $data
     * @return mixed
     * @see Tester::patchAdminApi
     */
    public function patchAdminApi(string $page, $data = [])
    {
    }

    /**
     * @param string $page
     * @return mixed
     * @see Tester::deleteAdminApi
     */
    public function deleteAdminApi(string $page)
    {
    }
}

class User
{
    /**
     * @return int|null
     * @see User::id
     */
    public function id()
    {
    }

    /**
     * @return UserModel
     * @see User::cur
     */
    public function cur()
    {
    }

    /**
     * 判断用户是否登录
     *
     * @return bool
     * @see User::isLogin
     */
    public function isLogin()
    {
    }

    /**
     * 检查用户是否登录
     *
     * @return Ret
     * @see User::checkLogin
     */
    public function checkLogin()
    {
    }

    /**
     * 根据用户账号密码,登录用户
     *
     * @param mixed $data
     * @return Ret
     * @see User::login
     */
    public function login($data)
    {
    }

    /**
     * 根据用户ID直接登录用户
     *
     * @param int $id
     * @return Ret
     * @see User::loginById
     */
    public function loginById($id)
    {
    }

    /**
     * 根据条件查找或创建用户,并登录
     *
     * @param mixed $conditions
     * @param array $data
     * @return $this
     * @see User::loginBy
     */
    public function loginBy($conditions, $data = [])
    {
    }

    /**
     * 根据用户对象登录用户
     *
     * @param UserModel $user
     * @return Ret
     * @see User::loginByModel
     */
    public function loginByModel(UserModel $user)
    {
    }

    /**
     * 销毁用户会话,退出登录
     *
     * @return Ret
     * @see User::logout
     */
    public function logout()
    {
    }

    /**
     * 当用户信息更改后,可以主动调用该方法,刷新会话中的数据
     *
     * @param UserModel $user
     * @return $this
     * @see User::refresh
     */
    public function refresh(UserModel $user)
    {
    }

    /**
     * 通过外部检查用户是否有某个权限
     *
     * @param string $permissionId
     * @return bool
     * @see UserModel::can
     */
    public function can($permissionId)
    {
    }

    /**
     * @param array|\ArrayAccess $req
     * @return Ret
     * @see UserModel::updatePassword
     */
    public function updatePassword($req)
    {
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $data
     * @return $this
     * @see Model::saveData
     */
    public function saveData($data = [])
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return array
     * @see Model::toRet
     */
    public function toRet(array $merge = [])
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see Model::getTable
     */
    public function getTable()
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     * @see Model::fromArray
     */
    public function fromArray($data)
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see Model::destroy
     */
    public function destroy($id = null)
    {
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see Model::set
     */
    public function set($name, $value = null, $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see Model::find
     */
    public function find($id)
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see Model::findOrFail
     */
    public function findOrFail($id)
    {
    }

    /**
     * Find a record by primary key, or init with the specified data if record not found
     *
     * @param int|string $id
     * @param array|object $data
     * @return $this
     * @see Model::findOrInit
     */
    public function findOrInit($id = null, $data = [])
    {
    }

    /**
     * Find a record by primary key, or save with the specified data if record not found
     *
     * @param int|string $id
     * @param array $data
     * @return $this
     * @see Model::findOrCreate
     */
    public function findOrCreate($id, $data = [])
    {
    }

    /**
     * @param array $attributes
     * @param array $data
     * @return $this
     * @see Model::findByOrCreate
     */
    public function findByOrCreate($attributes, $data = [])
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see Model::findAll
     */
    public function findAll($ids)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see Model::findBy
     */
    public function findBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see Model::findAllBy
     */
    public function findAllBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see Model::findOrInitBy
     */
    public function findOrInitBy($attributes, $data = [])
    {
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this
     * @throws \Exception
     * @see Model::findByOrFail
     */
    public function findByOrFail($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @deprecated Use `findFromReq` instead
     * @see Model::findFromRequest
     */
    public function findFromRequest($req = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @see Model::findFromReq
     */
    public function findFromReq($req = null)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see Model::first
     */
    public function first()
    {
    }

    /**
     * @return $this|array
     * @see Model::all
     */
    public function all()
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see Model::indexBy
     */
    public function indexBy($column)
    {
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @see QueryBuilder::getFields
     */
    public function getFields()
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see QueryBuilder::fetchAll
     */
    public function fetchAll($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public function pluck(string $column, string $index = null)
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public function chunk(int $count, callable $callback)
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public function cnt($column = '*')
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see QueryBuilder::update
     */
    public function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @see QueryBuilder::delete
     */
    public function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public function limit($limit)
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public function page($page)
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see QueryBuilder::select
     */
    public function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectDistinct
     */
    public function selectDistinct($columns)
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public function selectRaw($expression)
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectExcept
     */
    public function selectExcept($columns)
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see QueryBuilder::from
     */
    public function from($table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see QueryBuilder::table
     */
    public function table(string $table, $alias = null): self
    {
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
     * @see QueryBuilder::join
     */
    public function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER')
    {
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::innerJoin
     */
    public function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::leftJoin
     */
    public function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::rightJoin
     */
    public function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
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
     * @see QueryBuilder::where
     */
    public function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public function whereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::orWhereBetween
     */
    public function orWhereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public function whereIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public function whereNotIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public function whereNull($column)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public function whereNotNULL($column)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public function whereColumn($column, $opOrColumn2, $column2 = null)
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public function whereContains($column, $value, string $condition = 'AND')
    {
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public function whereNotContains($column, $value, string $condition = 'OR')
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see QueryBuilder::groupBy
     */
    public function groupBy($column)
    {
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
     * @see QueryBuilder::having
     */
    public function having($column, $operator, $value = null, $condition = 'AND')
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see QueryBuilder::orderBy
     */
    public function orderBy($column, $order = 'ASC')
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public function desc($field)
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public function asc($field)
    {
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @see QueryBuilder::resetSqlPart
     */
    public function resetSqlPart($name)
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public function forUpdate()
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public function forShare()
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public function lock($lock)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public function when($value, $callback, callable $default = null)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public function unless($value, callable $callback, callable $default = null)
    {
    }

    /**
     * @param callable $converter
     * @return $this
     * @see QueryBuilder::setInputIdentifierConverter
     */
    public function setInputIdentifierConverter(callable $converter)
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param false|int|null $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public function cache($seconds = null)
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see Model::unscoped
     */
    public function unscoped($scopes = [])
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::like
     */
    public function like($columns)
    {
    }
}

class UserModel
{
    /**
     * 通过外部检查用户是否有某个权限
     *
     * @param string $permissionId
     * @return bool
     * @see UserModel::can
     */
    public function can($permissionId)
    {
    }

    /**
     * @param array|\ArrayAccess $req
     * @return Ret
     * @see UserModel::updatePassword
     */
    public function updatePassword($req)
    {
    }

    /**
     * Returns the record data as array
     *
     * @param array|callable $returnFields A indexed array specified the fields to return
     * @param callable|null $prepend
     * @return array
     * @see Model::toArray
     */
    public function toArray($returnFields = [], callable $prepend = null)
    {
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $data
     * @return $this
     * @see Model::saveData
     */
    public function saveData($data = [])
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return array
     * @see Model::toRet
     */
    public function toRet(array $merge = [])
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see Model::getTable
     */
    public function getTable()
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     * @see Model::fromArray
     */
    public function fromArray($data)
    {
    }

    /**
     * Save the record or data to database
     *
     * @param array $data
     * @return $this
     * @see Model::save
     */
    public function save($data = [])
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see Model::destroy
     */
    public function destroy($id = null)
    {
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see Model::set
     */
    public function set($name, $value = null, $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see Model::find
     */
    public function find($id)
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see Model::findOrFail
     */
    public function findOrFail($id)
    {
    }

    /**
     * Find a record by primary key, or init with the specified data if record not found
     *
     * @param int|string $id
     * @param array|object $data
     * @return $this
     * @see Model::findOrInit
     */
    public function findOrInit($id = null, $data = [])
    {
    }

    /**
     * Find a record by primary key, or save with the specified data if record not found
     *
     * @param int|string $id
     * @param array $data
     * @return $this
     * @see Model::findOrCreate
     */
    public function findOrCreate($id, $data = [])
    {
    }

    /**
     * @param array $attributes
     * @param array $data
     * @return $this
     * @see Model::findByOrCreate
     */
    public function findByOrCreate($attributes, $data = [])
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see Model::findAll
     */
    public function findAll($ids)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see Model::findBy
     */
    public function findBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see Model::findAllBy
     */
    public function findAllBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see Model::findOrInitBy
     */
    public function findOrInitBy($attributes, $data = [])
    {
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this
     * @throws \Exception
     * @see Model::findByOrFail
     */
    public function findByOrFail($column, $operator = null, $value = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @deprecated Use `findFromReq` instead
     * @see Model::findFromRequest
     */
    public function findFromRequest($req = null)
    {
    }

    /**
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @see Model::findFromReq
     */
    public function findFromReq($req = null)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see Model::first
     */
    public function first()
    {
    }

    /**
     * @return $this|array
     * @see Model::all
     */
    public function all()
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see Model::indexBy
     */
    public function indexBy($column)
    {
    }

    /**
     * Returns the name of fields of current table
     *
     * @return array
     * @see QueryBuilder::getFields
     */
    public function getFields()
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see QueryBuilder::fetchAll
     */
    public function fetchAll($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public function pluck(string $column, string $index = null)
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public function chunk(int $count, callable $callback)
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public function cnt($column = '*')
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see QueryBuilder::update
     */
    public function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return mixed
     * @see QueryBuilder::delete
     */
    public function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public function limit($limit)
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public function page($page)
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see QueryBuilder::select
     */
    public function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectDistinct
     */
    public function selectDistinct($columns)
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public function selectRaw($expression)
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see QueryBuilder::selectExcept
     */
    public function selectExcept($columns)
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see QueryBuilder::from
     */
    public function from($table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see QueryBuilder::table
     */
    public function table(string $table, $alias = null): self
    {
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
     * @see QueryBuilder::join
     */
    public function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER')
    {
    }

    /**
     * Adds a inner join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::innerJoin
     */
    public function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a left join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::leftJoin
     */
    public function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
    }

    /**
     * Adds a right join to the query
     *
     * @param string $table The table name to join
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @return $this
     * @see QueryBuilder::rightJoin
     */
    public function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null)
    {
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
     * @see QueryBuilder::where
     */
    public function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public function whereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::orWhereBetween
     */
    public function orWhereBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public function whereIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public function whereNotIn($column, array $params)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public function whereNull($column)
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public function whereNotNULL($column)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param string $column
     * @param string $opOrColumn2
     * @param string|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public function whereColumn($column, $opOrColumn2, $column2 = null)
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public function whereContains($column, $value, string $condition = 'AND')
    {
    }

    /**
     * @param mixed $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public function whereNotContains($column, $value, string $condition = 'OR')
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see QueryBuilder::groupBy
     */
    public function groupBy($column)
    {
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
     * @see QueryBuilder::having
     */
    public function having($column, $operator, $value = null, $condition = 'AND')
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see QueryBuilder::orderBy
     */
    public function orderBy($column, $order = 'ASC')
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public function desc($field)
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public function asc($field)
    {
    }

    /**
     * Reset single SQL part
     *
     * @param string $name
     * @return $this
     * @see QueryBuilder::resetSqlPart
     */
    public function resetSqlPart($name)
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public function forUpdate()
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public function forShare()
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public function lock($lock)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public function when($value, $callback, callable $default = null)
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public function unless($value, callable $callback, callable $default = null)
    {
    }

    /**
     * @param callable $converter
     * @return $this
     * @see QueryBuilder::setInputIdentifierConverter
     */
    public function setInputIdentifierConverter(callable $converter)
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param false|int|null $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public function cache($seconds = null)
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see Model::unscoped
     */
    public function unscoped($scopes = [])
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::like
     */
    public function like($columns)
    {
    }
}
}
