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
     * @see AppModel::toArray
     */
    public static function toArray($returnFields = [], callable $prepend = null): array
    {
    }

    /**
     * Set each attribute value, without checking whether the column is fillable, and save the model
     *
     * @param iterable $attributes
     * @return $this
     * @see AppModel::saveAttributes
     */
    public static function saveAttributes(iterable $attributes = []): self
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return Ret
     * @see AppModel::toRet
     */
    public static function toRet(array $merge = []): \Wei\Ret
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see AppModel::getTable
     */
    public static function getTable(): string
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param iterable $array
     * @return $this
     * @see AppModel::fromArray
     */
    public static function fromArray(iterable $array): self
    {
    }

    /**
     * Save the record or data to database
     *
     * @param iterable $attributes
     * @return $this
     * @see AppModel::save
     */
    public static function save(iterable $attributes = []): self
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see AppModel::destroy
     */
    public static function destroy($id = null): self
    {
    }

    /**
     * Set the record field value
     *
     * @param string|int $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see AppModel::set
     */
    public static function set($name, $value = null, bool $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see AppModel::find
     */
    public static function find($id): ?self
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see AppModel::findOrFail
     */
    public static function findOrFail($id): self
    {
    }

    /**
     * Find a record by primary key, or init with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array|object $attributes
     * @return $this
     * @see AppModel::findOrInit
     */
    public static function findOrInit($id = null, $attributes = []): self
    {
    }

    /**
     * Find a record by primary key, or save with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array $attributes
     * @return $this
     * @see AppModel::findOrCreate
     */
    public static function findOrCreate($id, $attributes = []): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see AppModel::findByOrCreate
     */
    public static function findByOrCreate($attributes, $data = []): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see AppModel::findAll
     */
    public static function findAll(array $ids): self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see AppModel::findBy
     */
    public static function findBy($column, $operator = null, $value = null): ?self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see AppModel::findAllBy
     */
    public static function findAllBy($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see AppModel::findOrInitBy
     */
    public static function findOrInitBy(array $attributes, $data = []): self
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
     * @see AppModel::findByOrFail
     */
    public static function findByOrFail($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param Req|null $req
     * @return $this
     * @throws \Exception
     * @see AppModel::findFromReq
     */
    public static function findFromReq(\Wei\Req $req = null): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see AppModel::first
     */
    public static function first(): ?self
    {
    }

    /**
     * @return $this|$this[]
     * @see AppModel::all
     */
    public static function all(): self
    {
    }

    /**
     * Coll: Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @see AppModel::indexBy
     */
    public static function indexBy(string $column): self
    {
    }

    /**
     * Returns the name of columns of current table
     *
     * @return array
     * @see AppModel::getColumns
     */
    public static function getColumns(): array
    {
    }

    /**
     * Check if column name exists
     *
     * @param string|int|null $name
     * @return bool
     * @see AppModel::hasColumn
     */
    public static function hasColumn($name): bool
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see AppModel::fetch
     */
    public static function fetch($column = null, $operator = null, $value = null): ?array
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see AppModel::fetchAll
     */
    public static function fetchAll($column = null, $operator = null, $value = null): array
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see AppModel::pluck
     */
    public static function pluck(string $column, string $index = null): array
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see AppModel::chunk
     */
    public static function chunk(int $count, callable $callback): bool
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see AppModel::cnt
     */
    public static function cnt($column = '*'): int
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see AppModel::update
     */
    public static function update($set = [], $value = null): int
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return int
     * @see AppModel::delete
     */
    public static function delete($column = null, $operator = null, $value = null): int
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see AppModel::offset
     */
    public static function offset($offset): self
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see AppModel::limit
     */
    public static function limit($limit): self
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see AppModel::page
     */
    public static function page($page): self
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see AppModel::select
     */
    public static function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see AppModel::selectDistinct
     */
    public static function selectDistinct($columns): self
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see AppModel::selectRaw
     */
    public static function selectRaw($expression): self
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see AppModel::selectExcept
     */
    public static function selectExcept($columns): self
    {
    }

    /**
     * Specifies an item of the main table that is to be returned in the query result.
     * Default to all columns of the main table
     *
     * @param string $column
     * @return $this
     * @see AppModel::selectMain
     */
    public static function selectMain(string $column = '*'): self
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see AppModel::from
     */
    public static function from(string $table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see AppModel::table
     */
    public static function table(string $table, $alias = null): self
    {
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
     * @see AppModel::join
     */
    public static function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER'): self
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
     * @see AppModel::innerJoin
     */
    public static function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see AppModel::leftJoin
     */
    public static function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see AppModel::rightJoin
     */
    public static function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see AppModel::where
     */
    public static function where($column = null, $operator = null, $value = null): self
    {
    }

    /**
     * @param scalar $expression
     * @param mixed $params
     * @return $this
     * @see AppModel::whereRaw
     */
    public static function whereRaw($expression, $params = null): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see AppModel::whereBetween
     */
    public static function whereBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see AppModel::whereNotBetween
     */
    public static function whereNotBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see AppModel::whereIn
     */
    public static function whereIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see AppModel::whereNotIn
     */
    public static function whereNotIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see AppModel::whereNull
     */
    public static function whereNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see AppModel::whereNotNull
     */
    public static function whereNotNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see AppModel::whereDate
     */
    public static function whereDate(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see AppModel::whereMonth
     */
    public static function whereMonth(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see AppModel::whereDay
     */
    public static function whereDay(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see AppModel::whereYear
     */
    public static function whereYear(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see AppModel::whereTime
     */
    public static function whereTime(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrColumn2
     * @param mixed|null $column2
     * @return $this
     * @see AppModel::whereColumn
     */
    public static function whereColumn(string $column, $opOrColumn2, $column2 = null): self
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see AppModel::whereContains
     */
    public static function whereContains(string $column, $value, string $condition = 'AND'): self
    {
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see AppModel::whereNotContains
     */
    public static function whereNotContains(string $column, $value, string $condition = 'OR'): self
    {
    }

    /**
     * Search whether a column has a value other than the default value
     *
     * @param string $column
     * @param bool $has
     * @return $this
     * @see AppModel::whereHas
     */
    public static function whereHas(string $column, bool $has = true): self
    {
    }

    /**
     * Search whether a column dont have a value other than the default value
     *
     * @param string $column
     * @return $this
     * @see AppModel::whereNotHas
     */
    public static function whereNotHas(string $column): self
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see AppModel::groupBy
     */
    public static function groupBy($column): self
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
     * @see AppModel::having
     */
    public static function having($column, $operator, $value = null, $condition = 'AND'): self
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see AppModel::orderBy
     */
    public static function orderBy(string $column, $order = 'ASC'): self
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see AppModel::desc
     */
    public static function desc(string $field): self
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see AppModel::asc
     */
    public static function asc(string $field): self
    {
    }

    /**
     * @return $this
     * @see AppModel::forUpdate
     */
    public static function forUpdate(): self
    {
    }

    /**
     * @return $this
     * @see AppModel::forShare
     */
    public static function forShare(): self
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see AppModel::lock
     */
    public static function lock($lock): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see AppModel::when
     */
    public static function when($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see AppModel::unless
     */
    public static function unless($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see AppModel::setDbKeyConverter
     */
    public static function setDbKeyConverter(callable $converter = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see AppModel::setPhpKeyConverter
     */
    public static function setPhpKeyConverter(callable $converter = null): self
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param int|null $seconds
     * @return $this
     * @see AppModel::setCacheTime
     */
    public static function setCacheTime(?int $seconds): self
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see AppModel::unscoped
     */
    public static function unscoped($scopes = []): self
    {
    }

    /**
     * Check if the model method defines the "Relation" attribute (or the "@Relation" tag in doc comment)
     *
     * This method only checks whether the specified method has the "Relation" attribute,
     * and does not check the actual logic.
     * It is provided for external use to avoid directly calling `$this->$relation()` to cause attacks.
     *
     * @param string $method
     * @return bool
     * @see AppModel::isRelation
     */
    public static function isRelation(string $method): bool
    {
    }
}

class Cls
{
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

class IsModelExists
{
}

class Jwt
{
    /**
     * @return string
     * @see Jwt::getPrivateKey
     */
    public static function getPrivateKey()
    {
    }

    /**
     * @param string $privateKey
     * @return $this
     * @see Jwt::setPrivateKey
     */
    public static function setPrivateKey(string $privateKey)
    {
    }

    /**
     * @return string
     * @see Jwt::getPublicKey
     */
    public static function getPublicKey()
    {
    }

    /**
     * @param string $publicKey
     * @return $this
     * @see Jwt::setPublicKey
     */
    public static function setPublicKey(string $publicKey)
    {
    }

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

class PageRouter
{
}

class Plugin
{
    /**
     * Load service configs
     *
     * @param bool $refresh
     * @return $this
     * @see Plugin::loadConfig
     */
    public static function loadConfig($refresh = false)
    {
    }

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
     * @return string|null
     * @see QueryBuilder::getTable
     */
    public static function getTable(): ?string
    {
    }

    /**
     * Returns the name of columns of current table
     *
     * @return array
     * @see QueryBuilder::getColumns
     */
    public static function getColumns(): array
    {
    }

    /**
     * Check if column name exists
     *
     * @param string|int|null $name
     * @return bool
     * @see QueryBuilder::hasColumn
     */
    public static function hasColumn($name): bool
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
    public static function fetch($column = null, $operator = null, $value = null): ?array
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
    public static function fetchAll($column = null, $operator = null, $value = null): array
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return array|null
     * @see QueryBuilder::first
     */
    public static function first(): ?array
    {
    }

    /**
     * @return array
     * @see QueryBuilder::all
     */
    public static function all(): array
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public static function pluck(string $column, string $index = null): array
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public static function chunk(int $count, callable $callback): bool
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public static function cnt($column = '*'): int
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
    public static function update($set = [], $value = null): int
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return int
     * @see QueryBuilder::delete
     */
    public static function delete($column = null, $operator = null, $value = null): int
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public static function offset($offset): self
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public static function limit($limit): self
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public static function page($page): self
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
    public static function selectDistinct($columns): self
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public static function selectRaw($expression): self
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
    public static function selectExcept($columns): self
    {
    }

    /**
     * Specifies an item of the main table that is to be returned in the query result.
     * Default to all columns of the main table
     *
     * @param string $column
     * @return $this
     * @see QueryBuilder::selectMain
     */
    public static function selectMain(string $column = '*'): self
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
    public static function from(string $table, $alias = null): self
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
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @param string $type
     * @return $this
     * @see QueryBuilder::join
     */
    public static function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER'): self
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
    public static function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
    public static function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
    public static function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
    public static function where($column = null, $operator = null, $value = null): self
    {
    }

    /**
     * @param scalar $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public static function whereRaw($expression, $params = null): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public static function whereBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public static function whereNotBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public static function whereIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public static function whereNotIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public static function whereNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNull
     */
    public static function whereNotNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public static function whereDate(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public static function whereMonth(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public static function whereDay(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public static function whereYear(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public static function whereTime(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrColumn2
     * @param mixed|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public static function whereColumn(string $column, $opOrColumn2, $column2 = null): self
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public static function whereContains(string $column, $value, string $condition = 'AND'): self
    {
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public static function whereNotContains(string $column, $value, string $condition = 'OR'): self
    {
    }

    /**
     * Search whether a column has a value other than the default value
     *
     * @param string $column
     * @param bool $has
     * @return $this
     * @see QueryBuilder::whereHas
     */
    public static function whereHas(string $column, bool $has = true): self
    {
    }

    /**
     * Search whether a column dont have a value other than the default value
     *
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotHas
     */
    public static function whereNotHas(string $column): self
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
    public static function groupBy($column): self
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
    public static function having($column, $operator, $value = null, $condition = 'AND'): self
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
    public static function orderBy(string $column, $order = 'ASC'): self
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public static function desc(string $field): self
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public static function asc(string $field): self
    {
    }

    /**
     * Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @see QueryBuilder::indexBy
     */
    public static function indexBy(string $column): self
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public static function forUpdate(): self
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public static function forShare(): self
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public static function lock($lock): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public static function when($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public static function unless($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see QueryBuilder::setDbKeyConverter
     */
    public static function setDbKeyConverter(callable $converter = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see QueryBuilder::setPhpKeyConverter
     */
    public static function setPhpKeyConverter(callable $converter = null): self
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param int|null $seconds
     * @return $this
     * @see QueryBuilder::setCacheTime
     */
    public static function setCacheTime(?int $seconds): self
    {
    }
}

class Ret
{
    /**
     * {@inheritDoc}
     * @throws \Exception
     * @see Ret::err
     */
    public static function err($message, $code = null, $level = null)
    {
    }

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
     * Return operation failed result, and logs with a warning level
     *
     * @param string $message
     * @param int $code
     * @return $this
     * @see Ret::warning
     */
    public static function warning($message, $code = null)
    {
    }

    /**
     * Return operation failed result, and logs with an alert level
     *
     * @param string $message
     * @param int $code
     * @return $this
     * @see Ret::alert
     */
    public static function alert($message, $code = null)
    {
    }
}

class Session
{
}

class Str
{
    /**
     * @param string $word
     * @return string
     * @see Str::pluralize
     */
    public static function pluralize($word): string
    {
    }

    /**
     * @param string $word
     * @return string
     * @see Str::singularize
     */
    public static function singularize($word): string
    {
    }

    /**
     * Convert a input to snake case
     *
     * @param string $input
     * @param string $delimiter
     * @return string
     * @see Str::snake
     */
    public static function snake($input, string $delimiter = '_'): string
    {
    }

    /**
     * Convert a input to camel case
     *
     * @param string $input
     * @return string
     * @see Str::camel
     */
    public static function camel($input): string
    {
    }

    /**
     * Convert a input to dash case
     *
     * @param string $input
     * @return string
     * @see Str::dash
     */
    public static function dash($input): string
    {
    }
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
     * Set each attribute value, without checking whether the column is fillable, and save the model
     *
     * @param iterable $attributes
     * @return $this
     * @see UserModel::saveAttributes
     */
    public static function saveAttributes(iterable $attributes = []): self
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return Ret
     * @see UserModel::toRet
     */
    public static function toRet(array $merge = []): \Wei\Ret
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see UserModel::getTable
     */
    public static function getTable(): string
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param iterable $array
     * @return $this
     * @see UserModel::fromArray
     */
    public static function fromArray(iterable $array): self
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see UserModel::destroy
     */
    public static function destroy($id = null): self
    {
    }

    /**
     * Set the record field value
     *
     * @param string|int $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see UserModel::set
     */
    public static function set($name, $value = null, bool $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see UserModel::find
     */
    public static function find($id): ?self
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see UserModel::findOrFail
     */
    public static function findOrFail($id): self
    {
    }

    /**
     * Find a record by primary key, or init with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array|object $attributes
     * @return $this
     * @see UserModel::findOrInit
     */
    public static function findOrInit($id = null, $attributes = []): self
    {
    }

    /**
     * Find a record by primary key, or save with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array $attributes
     * @return $this
     * @see UserModel::findOrCreate
     */
    public static function findOrCreate($id, $attributes = []): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see UserModel::findByOrCreate
     */
    public static function findByOrCreate($attributes, $data = []): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see UserModel::findAll
     */
    public static function findAll(array $ids): self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see UserModel::findBy
     */
    public static function findBy($column, $operator = null, $value = null): ?self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see UserModel::findAllBy
     */
    public static function findAllBy($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see UserModel::findOrInitBy
     */
    public static function findOrInitBy(array $attributes, $data = []): self
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
     * @see UserModel::findByOrFail
     */
    public static function findByOrFail($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param Req|null $req
     * @return $this
     * @throws \Exception
     * @see UserModel::findFromReq
     */
    public static function findFromReq(\Wei\Req $req = null): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see UserModel::first
     */
    public static function first(): ?self
    {
    }

    /**
     * @return $this|$this[]
     * @see UserModel::all
     */
    public static function all(): self
    {
    }

    /**
     * Coll: Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @see UserModel::indexBy
     */
    public static function indexBy(string $column): self
    {
    }

    /**
     * Returns the name of columns of current table
     *
     * @return array
     * @see UserModel::getColumns
     */
    public static function getColumns(): array
    {
    }

    /**
     * Check if column name exists
     *
     * @param string|int|null $name
     * @return bool
     * @see UserModel::hasColumn
     */
    public static function hasColumn($name): bool
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see UserModel::fetch
     */
    public static function fetch($column = null, $operator = null, $value = null): ?array
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see UserModel::fetchAll
     */
    public static function fetchAll($column = null, $operator = null, $value = null): array
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see UserModel::pluck
     */
    public static function pluck(string $column, string $index = null): array
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see UserModel::chunk
     */
    public static function chunk(int $count, callable $callback): bool
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see UserModel::cnt
     */
    public static function cnt($column = '*'): int
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see UserModel::update
     */
    public static function update($set = [], $value = null): int
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return int
     * @see UserModel::delete
     */
    public static function delete($column = null, $operator = null, $value = null): int
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see UserModel::offset
     */
    public static function offset($offset): self
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see UserModel::limit
     */
    public static function limit($limit): self
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see UserModel::page
     */
    public static function page($page): self
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see UserModel::select
     */
    public static function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::selectDistinct
     */
    public static function selectDistinct($columns): self
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see UserModel::selectRaw
     */
    public static function selectRaw($expression): self
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see UserModel::selectExcept
     */
    public static function selectExcept($columns): self
    {
    }

    /**
     * Specifies an item of the main table that is to be returned in the query result.
     * Default to all columns of the main table
     *
     * @param string $column
     * @return $this
     * @see UserModel::selectMain
     */
    public static function selectMain(string $column = '*'): self
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see UserModel::from
     */
    public static function from(string $table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see UserModel::table
     */
    public static function table(string $table, $alias = null): self
    {
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
     * @see UserModel::join
     */
    public static function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER'): self
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
     * @see UserModel::innerJoin
     */
    public static function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::leftJoin
     */
    public static function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::rightJoin
     */
    public static function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::where
     */
    public static function where($column = null, $operator = null, $value = null): self
    {
    }

    /**
     * @param scalar $expression
     * @param mixed $params
     * @return $this
     * @see UserModel::whereRaw
     */
    public static function whereRaw($expression, $params = null): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereBetween
     */
    public static function whereBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereNotBetween
     */
    public static function whereNotBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereIn
     */
    public static function whereIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereNotIn
     */
    public static function whereNotIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see UserModel::whereNull
     */
    public static function whereNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see UserModel::whereNotNull
     */
    public static function whereNotNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereDate
     */
    public static function whereDate(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereMonth
     */
    public static function whereMonth(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereDay
     */
    public static function whereDay(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereYear
     */
    public static function whereYear(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereTime
     */
    public static function whereTime(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrColumn2
     * @param mixed|null $column2
     * @return $this
     * @see UserModel::whereColumn
     */
    public static function whereColumn(string $column, $opOrColumn2, $column2 = null): self
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see UserModel::whereContains
     */
    public static function whereContains(string $column, $value, string $condition = 'AND'): self
    {
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see UserModel::whereNotContains
     */
    public static function whereNotContains(string $column, $value, string $condition = 'OR'): self
    {
    }

    /**
     * Search whether a column has a value other than the default value
     *
     * @param string $column
     * @param bool $has
     * @return $this
     * @see UserModel::whereHas
     */
    public static function whereHas(string $column, bool $has = true): self
    {
    }

    /**
     * Search whether a column dont have a value other than the default value
     *
     * @param string $column
     * @return $this
     * @see UserModel::whereNotHas
     */
    public static function whereNotHas(string $column): self
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see UserModel::groupBy
     */
    public static function groupBy($column): self
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
     * @see UserModel::having
     */
    public static function having($column, $operator, $value = null, $condition = 'AND'): self
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see UserModel::orderBy
     */
    public static function orderBy(string $column, $order = 'ASC'): self
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see UserModel::desc
     */
    public static function desc(string $field): self
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see UserModel::asc
     */
    public static function asc(string $field): self
    {
    }

    /**
     * @return $this
     * @see UserModel::forUpdate
     */
    public static function forUpdate(): self
    {
    }

    /**
     * @return $this
     * @see UserModel::forShare
     */
    public static function forShare(): self
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see UserModel::lock
     */
    public static function lock($lock): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see UserModel::when
     */
    public static function when($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see UserModel::unless
     */
    public static function unless($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see UserModel::setDbKeyConverter
     */
    public static function setDbKeyConverter(callable $converter = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see UserModel::setPhpKeyConverter
     */
    public static function setPhpKeyConverter(callable $converter = null): self
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param int|null $seconds
     * @return $this
     * @see UserModel::setCacheTime
     */
    public static function setCacheTime(?int $seconds): self
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see UserModel::unscoped
     */
    public static function unscoped($scopes = []): self
    {
    }

    /**
     * Check if the model method defines the "Relation" attribute (or the "@Relation" tag in doc comment)
     *
     * This method only checks whether the specified method has the "Relation" attribute,
     * and does not check the actual logic.
     * It is provided for external use to avoid directly calling `$this->$relation()` to cause attacks.
     *
     * @param string $method
     * @return bool
     * @see UserModel::isRelation
     */
    public static function isRelation(string $method): bool
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::like
     */
    public static function like($columns): self
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
     * @see UserModel::toArray
     */
    public static function toArray($returnFields = [], callable $prepend = null): array
    {
    }

    /**
     * Set each attribute value, without checking whether the column is fillable, and save the model
     *
     * @param iterable $attributes
     * @return $this
     * @see UserModel::saveAttributes
     */
    public static function saveAttributes(iterable $attributes = []): self
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return Ret
     * @see UserModel::toRet
     */
    public static function toRet(array $merge = []): \Wei\Ret
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see UserModel::getTable
     */
    public static function getTable(): string
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param iterable $array
     * @return $this
     * @see UserModel::fromArray
     */
    public static function fromArray(iterable $array): self
    {
    }

    /**
     * Save the record or data to database
     *
     * @param iterable $attributes
     * @return $this
     * @see UserModel::save
     */
    public static function save(iterable $attributes = []): self
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see UserModel::destroy
     */
    public static function destroy($id = null): self
    {
    }

    /**
     * Set the record field value
     *
     * @param string|int $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see UserModel::set
     */
    public static function set($name, $value = null, bool $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see UserModel::find
     */
    public static function find($id): ?self
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see UserModel::findOrFail
     */
    public static function findOrFail($id): self
    {
    }

    /**
     * Find a record by primary key, or init with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array|object $attributes
     * @return $this
     * @see UserModel::findOrInit
     */
    public static function findOrInit($id = null, $attributes = []): self
    {
    }

    /**
     * Find a record by primary key, or save with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array $attributes
     * @return $this
     * @see UserModel::findOrCreate
     */
    public static function findOrCreate($id, $attributes = []): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see UserModel::findByOrCreate
     */
    public static function findByOrCreate($attributes, $data = []): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see UserModel::findAll
     */
    public static function findAll(array $ids): self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see UserModel::findBy
     */
    public static function findBy($column, $operator = null, $value = null): ?self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see UserModel::findAllBy
     */
    public static function findAllBy($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see UserModel::findOrInitBy
     */
    public static function findOrInitBy(array $attributes, $data = []): self
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
     * @see UserModel::findByOrFail
     */
    public static function findByOrFail($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param Req|null $req
     * @return $this
     * @throws \Exception
     * @see UserModel::findFromReq
     */
    public static function findFromReq(\Wei\Req $req = null): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see UserModel::first
     */
    public static function first(): ?self
    {
    }

    /**
     * @return $this|$this[]
     * @see UserModel::all
     */
    public static function all(): self
    {
    }

    /**
     * Coll: Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @see UserModel::indexBy
     */
    public static function indexBy(string $column): self
    {
    }

    /**
     * Returns the name of columns of current table
     *
     * @return array
     * @see UserModel::getColumns
     */
    public static function getColumns(): array
    {
    }

    /**
     * Check if column name exists
     *
     * @param string|int|null $name
     * @return bool
     * @see UserModel::hasColumn
     */
    public static function hasColumn($name): bool
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see UserModel::fetch
     */
    public static function fetch($column = null, $operator = null, $value = null): ?array
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see UserModel::fetchAll
     */
    public static function fetchAll($column = null, $operator = null, $value = null): array
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see UserModel::pluck
     */
    public static function pluck(string $column, string $index = null): array
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see UserModel::chunk
     */
    public static function chunk(int $count, callable $callback): bool
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see UserModel::cnt
     */
    public static function cnt($column = '*'): int
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see UserModel::update
     */
    public static function update($set = [], $value = null): int
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return int
     * @see UserModel::delete
     */
    public static function delete($column = null, $operator = null, $value = null): int
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see UserModel::offset
     */
    public static function offset($offset): self
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see UserModel::limit
     */
    public static function limit($limit): self
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see UserModel::page
     */
    public static function page($page): self
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see UserModel::select
     */
    public static function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::selectDistinct
     */
    public static function selectDistinct($columns): self
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see UserModel::selectRaw
     */
    public static function selectRaw($expression): self
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see UserModel::selectExcept
     */
    public static function selectExcept($columns): self
    {
    }

    /**
     * Specifies an item of the main table that is to be returned in the query result.
     * Default to all columns of the main table
     *
     * @param string $column
     * @return $this
     * @see UserModel::selectMain
     */
    public static function selectMain(string $column = '*'): self
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see UserModel::from
     */
    public static function from(string $table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see UserModel::table
     */
    public static function table(string $table, $alias = null): self
    {
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
     * @see UserModel::join
     */
    public static function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER'): self
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
     * @see UserModel::innerJoin
     */
    public static function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::leftJoin
     */
    public static function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::rightJoin
     */
    public static function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::where
     */
    public static function where($column = null, $operator = null, $value = null): self
    {
    }

    /**
     * @param scalar $expression
     * @param mixed $params
     * @return $this
     * @see UserModel::whereRaw
     */
    public static function whereRaw($expression, $params = null): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereBetween
     */
    public static function whereBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereNotBetween
     */
    public static function whereNotBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereIn
     */
    public static function whereIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereNotIn
     */
    public static function whereNotIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see UserModel::whereNull
     */
    public static function whereNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see UserModel::whereNotNull
     */
    public static function whereNotNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereDate
     */
    public static function whereDate(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereMonth
     */
    public static function whereMonth(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereDay
     */
    public static function whereDay(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereYear
     */
    public static function whereYear(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereTime
     */
    public static function whereTime(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrColumn2
     * @param mixed|null $column2
     * @return $this
     * @see UserModel::whereColumn
     */
    public static function whereColumn(string $column, $opOrColumn2, $column2 = null): self
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see UserModel::whereContains
     */
    public static function whereContains(string $column, $value, string $condition = 'AND'): self
    {
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see UserModel::whereNotContains
     */
    public static function whereNotContains(string $column, $value, string $condition = 'OR'): self
    {
    }

    /**
     * Search whether a column has a value other than the default value
     *
     * @param string $column
     * @param bool $has
     * @return $this
     * @see UserModel::whereHas
     */
    public static function whereHas(string $column, bool $has = true): self
    {
    }

    /**
     * Search whether a column dont have a value other than the default value
     *
     * @param string $column
     * @return $this
     * @see UserModel::whereNotHas
     */
    public static function whereNotHas(string $column): self
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see UserModel::groupBy
     */
    public static function groupBy($column): self
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
     * @see UserModel::having
     */
    public static function having($column, $operator, $value = null, $condition = 'AND'): self
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see UserModel::orderBy
     */
    public static function orderBy(string $column, $order = 'ASC'): self
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see UserModel::desc
     */
    public static function desc(string $field): self
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see UserModel::asc
     */
    public static function asc(string $field): self
    {
    }

    /**
     * @return $this
     * @see UserModel::forUpdate
     */
    public static function forUpdate(): self
    {
    }

    /**
     * @return $this
     * @see UserModel::forShare
     */
    public static function forShare(): self
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see UserModel::lock
     */
    public static function lock($lock): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see UserModel::when
     */
    public static function when($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see UserModel::unless
     */
    public static function unless($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see UserModel::setDbKeyConverter
     */
    public static function setDbKeyConverter(callable $converter = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see UserModel::setPhpKeyConverter
     */
    public static function setPhpKeyConverter(callable $converter = null): self
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param int|null $seconds
     * @return $this
     * @see UserModel::setCacheTime
     */
    public static function setCacheTime(?int $seconds): self
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see UserModel::unscoped
     */
    public static function unscoped($scopes = []): self
    {
    }

    /**
     * Check if the model method defines the "Relation" attribute (or the "@Relation" tag in doc comment)
     *
     * This method only checks whether the specified method has the "Relation" attribute,
     * and does not check the actual logic.
     * It is provided for external use to avoid directly calling `$this->$relation()` to cause attacks.
     *
     * @param string $method
     * @return bool
     * @see UserModel::isRelation
     */
    public static function isRelation(string $method): bool
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::like
     */
    public static function like($columns): self
    {
    }
}


namespace Wei;

class V
{
    /**
     * @return $this
     * @see \Miaoxing\Plugin\Service\IsModelExists::__invoke
     */
    public static function modelExists($model = null, $column = 'id')
    {
    }

    /**
     * @return $this
     * @see \Miaoxing\Plugin\Service\IsModelExists::__invoke
     */
    public static function notModelExists($model = null, $column = 'id')
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
     * @see AppModel::toArray
     */
    public function toArray($returnFields = [], callable $prepend = null): array
    {
    }

    /**
     * Set each attribute value, without checking whether the column is fillable, and save the model
     *
     * @param iterable $attributes
     * @return $this
     * @see AppModel::saveAttributes
     */
    public function saveAttributes(iterable $attributes = []): self
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return Ret
     * @see AppModel::toRet
     */
    public function toRet(array $merge = []): \Wei\Ret
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see AppModel::getTable
     */
    public function getTable(): string
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param iterable $array
     * @return $this
     * @see AppModel::fromArray
     */
    public function fromArray(iterable $array): self
    {
    }

    /**
     * Save the record or data to database
     *
     * @param iterable $attributes
     * @return $this
     * @see AppModel::save
     */
    public function save(iterable $attributes = []): self
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see AppModel::destroy
     */
    public function destroy($id = null): self
    {
    }

    /**
     * Set the record field value
     *
     * @param string|int $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see AppModel::set
     */
    public function set($name, $value = null, bool $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see AppModel::find
     */
    public function find($id): ?self
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see AppModel::findOrFail
     */
    public function findOrFail($id): self
    {
    }

    /**
     * Find a record by primary key, or init with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array|object $attributes
     * @return $this
     * @see AppModel::findOrInit
     */
    public function findOrInit($id = null, $attributes = []): self
    {
    }

    /**
     * Find a record by primary key, or save with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array $attributes
     * @return $this
     * @see AppModel::findOrCreate
     */
    public function findOrCreate($id, $attributes = []): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see AppModel::findByOrCreate
     */
    public function findByOrCreate($attributes, $data = []): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see AppModel::findAll
     */
    public function findAll(array $ids): self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see AppModel::findBy
     */
    public function findBy($column, $operator = null, $value = null): ?self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see AppModel::findAllBy
     */
    public function findAllBy($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see AppModel::findOrInitBy
     */
    public function findOrInitBy(array $attributes, $data = []): self
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
     * @see AppModel::findByOrFail
     */
    public function findByOrFail($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param Req|null $req
     * @return $this
     * @throws \Exception
     * @see AppModel::findFromReq
     */
    public function findFromReq(\Wei\Req $req = null): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see AppModel::first
     */
    public function first(): ?self
    {
    }

    /**
     * @return $this|$this[]
     * @see AppModel::all
     */
    public function all(): self
    {
    }

    /**
     * Coll: Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @see AppModel::indexBy
     */
    public function indexBy(string $column): self
    {
    }

    /**
     * Returns the name of columns of current table
     *
     * @return array
     * @see AppModel::getColumns
     */
    public function getColumns(): array
    {
    }

    /**
     * Check if column name exists
     *
     * @param string|int|null $name
     * @return bool
     * @see AppModel::hasColumn
     */
    public function hasColumn($name): bool
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see AppModel::fetch
     */
    public function fetch($column = null, $operator = null, $value = null): ?array
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see AppModel::fetchAll
     */
    public function fetchAll($column = null, $operator = null, $value = null): array
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see AppModel::pluck
     */
    public function pluck(string $column, string $index = null): array
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see AppModel::chunk
     */
    public function chunk(int $count, callable $callback): bool
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see AppModel::cnt
     */
    public function cnt($column = '*'): int
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see AppModel::update
     */
    public function update($set = [], $value = null): int
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return int
     * @see AppModel::delete
     */
    public function delete($column = null, $operator = null, $value = null): int
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see AppModel::offset
     */
    public function offset($offset): self
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see AppModel::limit
     */
    public function limit($limit): self
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see AppModel::page
     */
    public function page($page): self
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see AppModel::select
     */
    public function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see AppModel::selectDistinct
     */
    public function selectDistinct($columns): self
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see AppModel::selectRaw
     */
    public function selectRaw($expression): self
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see AppModel::selectExcept
     */
    public function selectExcept($columns): self
    {
    }

    /**
     * Specifies an item of the main table that is to be returned in the query result.
     * Default to all columns of the main table
     *
     * @param string $column
     * @return $this
     * @see AppModel::selectMain
     */
    public function selectMain(string $column = '*'): self
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see AppModel::from
     */
    public function from(string $table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see AppModel::table
     */
    public function table(string $table, $alias = null): self
    {
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
     * @see AppModel::join
     */
    public function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER'): self
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
     * @see AppModel::innerJoin
     */
    public function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see AppModel::leftJoin
     */
    public function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see AppModel::rightJoin
     */
    public function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see AppModel::where
     */
    public function where($column = null, $operator = null, $value = null): self
    {
    }

    /**
     * @param scalar $expression
     * @param mixed $params
     * @return $this
     * @see AppModel::whereRaw
     */
    public function whereRaw($expression, $params = null): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see AppModel::whereBetween
     */
    public function whereBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see AppModel::whereNotBetween
     */
    public function whereNotBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see AppModel::whereIn
     */
    public function whereIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see AppModel::whereNotIn
     */
    public function whereNotIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see AppModel::whereNull
     */
    public function whereNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see AppModel::whereNotNull
     */
    public function whereNotNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see AppModel::whereDate
     */
    public function whereDate(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see AppModel::whereMonth
     */
    public function whereMonth(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see AppModel::whereDay
     */
    public function whereDay(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see AppModel::whereYear
     */
    public function whereYear(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see AppModel::whereTime
     */
    public function whereTime(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrColumn2
     * @param mixed|null $column2
     * @return $this
     * @see AppModel::whereColumn
     */
    public function whereColumn(string $column, $opOrColumn2, $column2 = null): self
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see AppModel::whereContains
     */
    public function whereContains(string $column, $value, string $condition = 'AND'): self
    {
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see AppModel::whereNotContains
     */
    public function whereNotContains(string $column, $value, string $condition = 'OR'): self
    {
    }

    /**
     * Search whether a column has a value other than the default value
     *
     * @param string $column
     * @param bool $has
     * @return $this
     * @see AppModel::whereHas
     */
    public function whereHas(string $column, bool $has = true): self
    {
    }

    /**
     * Search whether a column dont have a value other than the default value
     *
     * @param string $column
     * @return $this
     * @see AppModel::whereNotHas
     */
    public function whereNotHas(string $column): self
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see AppModel::groupBy
     */
    public function groupBy($column): self
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
     * @see AppModel::having
     */
    public function having($column, $operator, $value = null, $condition = 'AND'): self
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see AppModel::orderBy
     */
    public function orderBy(string $column, $order = 'ASC'): self
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see AppModel::desc
     */
    public function desc(string $field): self
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see AppModel::asc
     */
    public function asc(string $field): self
    {
    }

    /**
     * @return $this
     * @see AppModel::forUpdate
     */
    public function forUpdate(): self
    {
    }

    /**
     * @return $this
     * @see AppModel::forShare
     */
    public function forShare(): self
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see AppModel::lock
     */
    public function lock($lock): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see AppModel::when
     */
    public function when($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see AppModel::unless
     */
    public function unless($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see AppModel::setDbKeyConverter
     */
    public function setDbKeyConverter(callable $converter = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see AppModel::setPhpKeyConverter
     */
    public function setPhpKeyConverter(callable $converter = null): self
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param int|null $seconds
     * @return $this
     * @see AppModel::setCacheTime
     */
    public function setCacheTime(?int $seconds): self
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see AppModel::unscoped
     */
    public function unscoped($scopes = []): self
    {
    }

    /**
     * Check if the model method defines the "Relation" attribute (or the "@Relation" tag in doc comment)
     *
     * This method only checks whether the specified method has the "Relation" attribute,
     * and does not check the actual logic.
     * It is provided for external use to avoid directly calling `$this->$relation()` to cause attacks.
     *
     * @param string $method
     * @return bool
     * @see AppModel::isRelation
     */
    public function isRelation(string $method): bool
    {
    }
}

class Cls
{
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

class IsModelExists
{
}

class Jwt
{
    /**
     * @return string
     * @see Jwt::getPrivateKey
     */
    public function getPrivateKey()
    {
    }

    /**
     * @param string $privateKey
     * @return $this
     * @see Jwt::setPrivateKey
     */
    public function setPrivateKey(string $privateKey)
    {
    }

    /**
     * @return string
     * @see Jwt::getPublicKey
     */
    public function getPublicKey()
    {
    }

    /**
     * @param string $publicKey
     * @return $this
     * @see Jwt::setPublicKey
     */
    public function setPublicKey(string $publicKey)
    {
    }

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

class PageRouter
{
}

class Plugin
{
    /**
     * Load service configs
     *
     * @param bool $refresh
     * @return $this
     * @see Plugin::loadConfig
     */
    public function loadConfig($refresh = false)
    {
    }

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
     * @return string|null
     * @see QueryBuilder::getTable
     */
    public function getTable(): ?string
    {
    }

    /**
     * Returns the name of columns of current table
     *
     * @return array
     * @see QueryBuilder::getColumns
     */
    public function getColumns(): array
    {
    }

    /**
     * Check if column name exists
     *
     * @param string|int|null $name
     * @return bool
     * @see QueryBuilder::hasColumn
     */
    public function hasColumn($name): bool
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
    public function fetch($column = null, $operator = null, $value = null): ?array
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
    public function fetchAll($column = null, $operator = null, $value = null): array
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return array|null
     * @see QueryBuilder::first
     */
    public function first(): ?array
    {
    }

    /**
     * @return array
     * @see QueryBuilder::all
     */
    public function all(): array
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see QueryBuilder::pluck
     */
    public function pluck(string $column, string $index = null): array
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see QueryBuilder::chunk
     */
    public function chunk(int $count, callable $callback): bool
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see QueryBuilder::cnt
     */
    public function cnt($column = '*'): int
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
    public function update($set = [], $value = null): int
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return int
     * @see QueryBuilder::delete
     */
    public function delete($column = null, $operator = null, $value = null): int
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public function offset($offset): self
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see QueryBuilder::limit
     */
    public function limit($limit): self
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see QueryBuilder::page
     */
    public function page($page): self
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
    public function selectDistinct($columns): self
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see QueryBuilder::selectRaw
     */
    public function selectRaw($expression): self
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
    public function selectExcept($columns): self
    {
    }

    /**
     * Specifies an item of the main table that is to be returned in the query result.
     * Default to all columns of the main table
     *
     * @param string $column
     * @return $this
     * @see QueryBuilder::selectMain
     */
    public function selectMain(string $column = '*'): self
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
    public function from(string $table, $alias = null): self
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
     * @param string|null $first
     * @param string $operator
     * @param string|null $second
     * @param string $type
     * @return $this
     * @see QueryBuilder::join
     */
    public function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER'): self
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
    public function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
    public function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
    public function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
    public function where($column = null, $operator = null, $value = null): self
    {
    }

    /**
     * @param scalar $expression
     * @param mixed $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public function whereRaw($expression, $params = null): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public function whereBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public function whereNotBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public function whereIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public function whereNotIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public function whereNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotNull
     */
    public function whereNotNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public function whereDate(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public function whereMonth(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public function whereDay(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public function whereYear(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public function whereTime(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrColumn2
     * @param mixed|null $column2
     * @return $this
     * @see QueryBuilder::whereColumn
     */
    public function whereColumn(string $column, $opOrColumn2, $column2 = null): self
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereContains
     */
    public function whereContains(string $column, $value, string $condition = 'AND'): self
    {
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see QueryBuilder::whereNotContains
     */
    public function whereNotContains(string $column, $value, string $condition = 'OR'): self
    {
    }

    /**
     * Search whether a column has a value other than the default value
     *
     * @param string $column
     * @param bool $has
     * @return $this
     * @see QueryBuilder::whereHas
     */
    public function whereHas(string $column, bool $has = true): self
    {
    }

    /**
     * Search whether a column dont have a value other than the default value
     *
     * @param string $column
     * @return $this
     * @see QueryBuilder::whereNotHas
     */
    public function whereNotHas(string $column): self
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
    public function groupBy($column): self
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
    public function having($column, $operator, $value = null, $condition = 'AND'): self
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
    public function orderBy(string $column, $order = 'ASC'): self
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::desc
     */
    public function desc(string $field): self
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see QueryBuilder::asc
     */
    public function asc(string $field): self
    {
    }

    /**
     * Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @see QueryBuilder::indexBy
     */
    public function indexBy(string $column): self
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forUpdate
     */
    public function forUpdate(): self
    {
    }

    /**
     * @return $this
     * @see QueryBuilder::forShare
     */
    public function forShare(): self
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see QueryBuilder::lock
     */
    public function lock($lock): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::when
     */
    public function when($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see QueryBuilder::unless
     */
    public function unless($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see QueryBuilder::setDbKeyConverter
     */
    public function setDbKeyConverter(callable $converter = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see QueryBuilder::setPhpKeyConverter
     */
    public function setPhpKeyConverter(callable $converter = null): self
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param int|null $seconds
     * @return $this
     * @see QueryBuilder::setCacheTime
     */
    public function setCacheTime(?int $seconds): self
    {
    }
}

class Ret
{
    /**
     * {@inheritDoc}
     * @throws \Exception
     * @see Ret::err
     */
    public function err($message, $code = null, $level = null)
    {
    }

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
     * Return operation failed result, and logs with a warning level
     *
     * @param string $message
     * @param int $code
     * @return $this
     * @see Ret::warning
     */
    public function warning($message, $code = null)
    {
    }

    /**
     * Return operation failed result, and logs with an alert level
     *
     * @param string $message
     * @param int $code
     * @return $this
     * @see Ret::alert
     */
    public function alert($message, $code = null)
    {
    }
}

class Session
{
}

class Str
{
    /**
     * @param string $word
     * @return string
     * @see Str::pluralize
     */
    public function pluralize($word): string
    {
    }

    /**
     * @param string $word
     * @return string
     * @see Str::singularize
     */
    public function singularize($word): string
    {
    }

    /**
     * Convert a input to snake case
     *
     * @param string $input
     * @param string $delimiter
     * @return string
     * @see Str::snake
     */
    public function snake($input, string $delimiter = '_'): string
    {
    }

    /**
     * Convert a input to camel case
     *
     * @param string $input
     * @return string
     * @see Str::camel
     */
    public function camel($input): string
    {
    }

    /**
     * Convert a input to dash case
     *
     * @param string $input
     * @return string
     * @see Str::dash
     */
    public function dash($input): string
    {
    }
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
     * Set each attribute value, without checking whether the column is fillable, and save the model
     *
     * @param iterable $attributes
     * @return $this
     * @see UserModel::saveAttributes
     */
    public function saveAttributes(iterable $attributes = []): self
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return Ret
     * @see UserModel::toRet
     */
    public function toRet(array $merge = []): \Wei\Ret
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see UserModel::getTable
     */
    public function getTable(): string
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param iterable $array
     * @return $this
     * @see UserModel::fromArray
     */
    public function fromArray(iterable $array): self
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see UserModel::destroy
     */
    public function destroy($id = null): self
    {
    }

    /**
     * Set the record field value
     *
     * @param string|int $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see UserModel::set
     */
    public function set($name, $value = null, bool $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see UserModel::find
     */
    public function find($id): ?self
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see UserModel::findOrFail
     */
    public function findOrFail($id): self
    {
    }

    /**
     * Find a record by primary key, or init with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array|object $attributes
     * @return $this
     * @see UserModel::findOrInit
     */
    public function findOrInit($id = null, $attributes = []): self
    {
    }

    /**
     * Find a record by primary key, or save with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array $attributes
     * @return $this
     * @see UserModel::findOrCreate
     */
    public function findOrCreate($id, $attributes = []): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see UserModel::findByOrCreate
     */
    public function findByOrCreate($attributes, $data = []): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see UserModel::findAll
     */
    public function findAll(array $ids): self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see UserModel::findBy
     */
    public function findBy($column, $operator = null, $value = null): ?self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see UserModel::findAllBy
     */
    public function findAllBy($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see UserModel::findOrInitBy
     */
    public function findOrInitBy(array $attributes, $data = []): self
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
     * @see UserModel::findByOrFail
     */
    public function findByOrFail($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param Req|null $req
     * @return $this
     * @throws \Exception
     * @see UserModel::findFromReq
     */
    public function findFromReq(\Wei\Req $req = null): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see UserModel::first
     */
    public function first(): ?self
    {
    }

    /**
     * @return $this|$this[]
     * @see UserModel::all
     */
    public function all(): self
    {
    }

    /**
     * Coll: Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @see UserModel::indexBy
     */
    public function indexBy(string $column): self
    {
    }

    /**
     * Returns the name of columns of current table
     *
     * @return array
     * @see UserModel::getColumns
     */
    public function getColumns(): array
    {
    }

    /**
     * Check if column name exists
     *
     * @param string|int|null $name
     * @return bool
     * @see UserModel::hasColumn
     */
    public function hasColumn($name): bool
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see UserModel::fetch
     */
    public function fetch($column = null, $operator = null, $value = null): ?array
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see UserModel::fetchAll
     */
    public function fetchAll($column = null, $operator = null, $value = null): array
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see UserModel::pluck
     */
    public function pluck(string $column, string $index = null): array
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see UserModel::chunk
     */
    public function chunk(int $count, callable $callback): bool
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see UserModel::cnt
     */
    public function cnt($column = '*'): int
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see UserModel::update
     */
    public function update($set = [], $value = null): int
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return int
     * @see UserModel::delete
     */
    public function delete($column = null, $operator = null, $value = null): int
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see UserModel::offset
     */
    public function offset($offset): self
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see UserModel::limit
     */
    public function limit($limit): self
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see UserModel::page
     */
    public function page($page): self
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see UserModel::select
     */
    public function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::selectDistinct
     */
    public function selectDistinct($columns): self
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see UserModel::selectRaw
     */
    public function selectRaw($expression): self
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see UserModel::selectExcept
     */
    public function selectExcept($columns): self
    {
    }

    /**
     * Specifies an item of the main table that is to be returned in the query result.
     * Default to all columns of the main table
     *
     * @param string $column
     * @return $this
     * @see UserModel::selectMain
     */
    public function selectMain(string $column = '*'): self
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see UserModel::from
     */
    public function from(string $table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see UserModel::table
     */
    public function table(string $table, $alias = null): self
    {
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
     * @see UserModel::join
     */
    public function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER'): self
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
     * @see UserModel::innerJoin
     */
    public function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::leftJoin
     */
    public function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::rightJoin
     */
    public function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::where
     */
    public function where($column = null, $operator = null, $value = null): self
    {
    }

    /**
     * @param scalar $expression
     * @param mixed $params
     * @return $this
     * @see UserModel::whereRaw
     */
    public function whereRaw($expression, $params = null): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereBetween
     */
    public function whereBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereNotBetween
     */
    public function whereNotBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereIn
     */
    public function whereIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereNotIn
     */
    public function whereNotIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see UserModel::whereNull
     */
    public function whereNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see UserModel::whereNotNull
     */
    public function whereNotNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereDate
     */
    public function whereDate(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereMonth
     */
    public function whereMonth(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereDay
     */
    public function whereDay(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereYear
     */
    public function whereYear(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereTime
     */
    public function whereTime(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrColumn2
     * @param mixed|null $column2
     * @return $this
     * @see UserModel::whereColumn
     */
    public function whereColumn(string $column, $opOrColumn2, $column2 = null): self
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see UserModel::whereContains
     */
    public function whereContains(string $column, $value, string $condition = 'AND'): self
    {
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see UserModel::whereNotContains
     */
    public function whereNotContains(string $column, $value, string $condition = 'OR'): self
    {
    }

    /**
     * Search whether a column has a value other than the default value
     *
     * @param string $column
     * @param bool $has
     * @return $this
     * @see UserModel::whereHas
     */
    public function whereHas(string $column, bool $has = true): self
    {
    }

    /**
     * Search whether a column dont have a value other than the default value
     *
     * @param string $column
     * @return $this
     * @see UserModel::whereNotHas
     */
    public function whereNotHas(string $column): self
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see UserModel::groupBy
     */
    public function groupBy($column): self
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
     * @see UserModel::having
     */
    public function having($column, $operator, $value = null, $condition = 'AND'): self
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see UserModel::orderBy
     */
    public function orderBy(string $column, $order = 'ASC'): self
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see UserModel::desc
     */
    public function desc(string $field): self
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see UserModel::asc
     */
    public function asc(string $field): self
    {
    }

    /**
     * @return $this
     * @see UserModel::forUpdate
     */
    public function forUpdate(): self
    {
    }

    /**
     * @return $this
     * @see UserModel::forShare
     */
    public function forShare(): self
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see UserModel::lock
     */
    public function lock($lock): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see UserModel::when
     */
    public function when($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see UserModel::unless
     */
    public function unless($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see UserModel::setDbKeyConverter
     */
    public function setDbKeyConverter(callable $converter = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see UserModel::setPhpKeyConverter
     */
    public function setPhpKeyConverter(callable $converter = null): self
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param int|null $seconds
     * @return $this
     * @see UserModel::setCacheTime
     */
    public function setCacheTime(?int $seconds): self
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see UserModel::unscoped
     */
    public function unscoped($scopes = []): self
    {
    }

    /**
     * Check if the model method defines the "Relation" attribute (or the "@Relation" tag in doc comment)
     *
     * This method only checks whether the specified method has the "Relation" attribute,
     * and does not check the actual logic.
     * It is provided for external use to avoid directly calling `$this->$relation()` to cause attacks.
     *
     * @param string $method
     * @return bool
     * @see UserModel::isRelation
     */
    public function isRelation(string $method): bool
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::like
     */
    public function like($columns): self
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
     * @see UserModel::toArray
     */
    public function toArray($returnFields = [], callable $prepend = null): array
    {
    }

    /**
     * Set each attribute value, without checking whether the column is fillable, and save the model
     *
     * @param iterable $attributes
     * @return $this
     * @see UserModel::saveAttributes
     */
    public function saveAttributes(iterable $attributes = []): self
    {
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return Ret
     * @see UserModel::toRet
     */
    public function toRet(array $merge = []): \Wei\Ret
    {
    }

    /**
     * Return the record table name
     *
     * @return string
     * @see UserModel::getTable
     */
    public function getTable(): string
    {
    }

    /**
     * Import a PHP array in this record
     *
     * @param iterable $array
     * @return $this
     * @see UserModel::fromArray
     */
    public function fromArray(iterable $array): self
    {
    }

    /**
     * Save the record or data to database
     *
     * @param iterable $attributes
     * @return $this
     * @see UserModel::save
     */
    public function save(iterable $attributes = []): self
    {
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @see UserModel::destroy
     */
    public function destroy($id = null): self
    {
    }

    /**
     * Set the record field value
     *
     * @param string|int $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @see UserModel::set
     */
    public function set($name, $value = null, bool $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string|array|null $id
     * @return $this|null
     * @see UserModel::find
     */
    public function find($id): ?self
    {
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @see UserModel::findOrFail
     */
    public function findOrFail($id): self
    {
    }

    /**
     * Find a record by primary key, or init with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array|object $attributes
     * @return $this
     * @see UserModel::findOrInit
     */
    public function findOrInit($id = null, $attributes = []): self
    {
    }

    /**
     * Find a record by primary key, or save with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array $attributes
     * @return $this
     * @see UserModel::findOrCreate
     */
    public function findOrCreate($id, $attributes = []): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see UserModel::findByOrCreate
     */
    public function findByOrCreate($attributes, $data = []): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @see UserModel::findAll
     */
    public function findAll(array $ids): self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @see UserModel::findBy
     */
    public function findBy($column, $operator = null, $value = null): ?self
    {
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @see UserModel::findAllBy
     */
    public function findAllBy($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @see UserModel::findOrInitBy
     */
    public function findOrInitBy(array $attributes, $data = []): self
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
     * @see UserModel::findByOrFail
     */
    public function findByOrFail($column, $operator = null, $value = null): self
    {
    }

    /**
     * @param Req|null $req
     * @return $this
     * @throws \Exception
     * @see UserModel::findFromReq
     */
    public function findFromReq(\Wei\Req $req = null): self
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @see UserModel::first
     */
    public function first(): ?self
    {
    }

    /**
     * @return $this|$this[]
     * @see UserModel::all
     */
    public function all(): self
    {
    }

    /**
     * Coll: Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @see UserModel::indexBy
     */
    public function indexBy(string $column): self
    {
    }

    /**
     * Returns the name of columns of current table
     *
     * @return array
     * @see UserModel::getColumns
     */
    public function getColumns(): array
    {
    }

    /**
     * Check if column name exists
     *
     * @param string|int|null $name
     * @return bool
     * @see UserModel::hasColumn
     */
    public function hasColumn($name): bool
    {
    }

    /**
     * Executes the generated query and returns the first array result
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array|null
     * @see UserModel::fetch
     */
    public function fetch($column = null, $operator = null, $value = null): ?array
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return array
     * @see UserModel::fetchAll
     */
    public function fetchAll($column = null, $operator = null, $value = null): array
    {
    }

    /**
     * @param string $column
     * @param string|null $index
     * @return array
     * @see UserModel::pluck
     */
    public function pluck(string $column, string $index = null): array
    {
    }

    /**
     * @param int $count
     * @param callable $callback
     * @return bool
     * @see UserModel::chunk
     */
    public function chunk(int $count, callable $callback): bool
    {
    }

    /**
     * Executes a COUNT query to receive the rows number
     *
     * @param string $column
     * @return int
     * @see UserModel::cnt
     */
    public function cnt($column = '*'): int
    {
    }

    /**
     * Execute a update query with specified data
     *
     * @param array|string $set
     * @param mixed $value
     * @return int
     * @see UserModel::update
     */
    public function update($set = [], $value = null): int
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed|null $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return int
     * @see UserModel::delete
     */
    public function delete($column = null, $operator = null, $value = null): int
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param int|float|string $offset The first result to return
     * @return $this
     * @see UserModel::offset
     */
    public function offset($offset): self
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param int|float|string $limit The maximum number of results to retrieve
     * @return $this
     * @see UserModel::limit
     */
    public function limit($limit): self
    {
    }

    /**
     * Sets the page number, the "OFFSET" value is equals "($page - 1) * LIMIT"
     *
     * @param int $page The page number
     * @return $this
     * @see UserModel::page
     */
    public function page($page): self
    {
    }

    /**
     * Specifies an item that is to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns the selection expressions
     * @return $this
     * @see UserModel::select
     */
    public function select($columns = ['*']): self
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::selectDistinct
     */
    public function selectDistinct($columns): self
    {
    }

    /**
     * @param string $expression
     * @return $this
     * @see UserModel::selectRaw
     */
    public function selectRaw($expression): self
    {
    }

    /**
     * Specifies columns that are not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param array|string $columns
     * @return $this
     * @see UserModel::selectExcept
     */
    public function selectExcept($columns): self
    {
    }

    /**
     * Specifies an item of the main table that is to be returned in the query result.
     * Default to all columns of the main table
     *
     * @param string $column
     * @return $this
     * @see UserModel::selectMain
     */
    public function selectMain(string $column = '*'): self
    {
    }

    /**
     * Sets table for FROM query
     *
     * @param string $table
     * @param string|null $alias
     * @return $this
     * @see UserModel::from
     */
    public function from(string $table, $alias = null): self
    {
    }

    /**
     * @param string $table
     * @param mixed|null $alias
     * @return $this
     * @see UserModel::table
     */
    public function table(string $table, $alias = null): self
    {
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
     * @see UserModel::join
     */
    public function join(string $table, string $first = null, string $operator = '=', string $second = null, string $type = 'INNER'): self
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
     * @see UserModel::innerJoin
     */
    public function innerJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::leftJoin
     */
    public function leftJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::rightJoin
     */
    public function rightJoin(string $table, string $first = null, string $operator = '=', string $second = null): self
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
     * @see UserModel::where
     */
    public function where($column = null, $operator = null, $value = null): self
    {
    }

    /**
     * @param scalar $expression
     * @param mixed $params
     * @return $this
     * @see UserModel::whereRaw
     */
    public function whereRaw($expression, $params = null): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereBetween
     */
    public function whereBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereNotBetween
     */
    public function whereNotBetween(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereIn
     */
    public function whereIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @param array $params
     * @return $this
     * @see UserModel::whereNotIn
     */
    public function whereNotIn(string $column, array $params): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see UserModel::whereNull
     */
    public function whereNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @return $this
     * @see UserModel::whereNotNull
     */
    public function whereNotNull(string $column): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereDate
     */
    public function whereDate(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereMonth
     */
    public function whereMonth(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereDay
     */
    public function whereDay(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereYear
     */
    public function whereYear(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrValue
     * @param mixed|null $value
     * @return $this
     * @see UserModel::whereTime
     */
    public function whereTime(string $column, $opOrValue, $value = null): self
    {
    }

    /**
     * @param string $column
     * @param mixed $opOrColumn2
     * @param mixed|null $column2
     * @return $this
     * @see UserModel::whereColumn
     */
    public function whereColumn(string $column, $opOrColumn2, $column2 = null): self
    {
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see UserModel::whereContains
     */
    public function whereContains(string $column, $value, string $condition = 'AND'): self
    {
    }

    /**
     * @param string $column
     * @param mixed $value
     * @param string $condition
     * @return $this
     * @see UserModel::whereNotContains
     */
    public function whereNotContains(string $column, $value, string $condition = 'OR'): self
    {
    }

    /**
     * Search whether a column has a value other than the default value
     *
     * @param string $column
     * @param bool $has
     * @return $this
     * @see UserModel::whereHas
     */
    public function whereHas(string $column, bool $has = true): self
    {
    }

    /**
     * Search whether a column dont have a value other than the default value
     *
     * @param string $column
     * @return $this
     * @see UserModel::whereNotHas
     */
    public function whereNotHas(string $column): self
    {
    }

    /**
     * Specifies a grouping over the results of the query.
     * Replaces any previously specified groupings, if any.
     *
     * @param mixed $column the grouping column
     * @return $this
     * @see UserModel::groupBy
     */
    public function groupBy($column): self
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
     * @see UserModel::having
     */
    public function having($column, $operator, $value = null, $condition = 'AND'): self
    {
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $column the ordering expression
     * @param string $order the ordering direction
     * @return $this
     * @see UserModel::orderBy
     */
    public function orderBy(string $column, $order = 'ASC'): self
    {
    }

    /**
     * Adds a DESC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see UserModel::desc
     */
    public function desc(string $field): self
    {
    }

    /**
     * Add an ASC ordering to the query
     *
     * @param string $field The name of field
     * @return $this
     * @see UserModel::asc
     */
    public function asc(string $field): self
    {
    }

    /**
     * @return $this
     * @see UserModel::forUpdate
     */
    public function forUpdate(): self
    {
    }

    /**
     * @return $this
     * @see UserModel::forShare
     */
    public function forShare(): self
    {
    }

    /**
     * @param string|bool $lock
     * @return $this
     * @see UserModel::lock
     */
    public function lock($lock): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see UserModel::when
     */
    public function when($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param mixed $value
     * @param callable $callback
     * @param callable|null $default
     * @return $this
     * @see UserModel::unless
     */
    public function unless($value, callable $callback, callable $default = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see UserModel::setDbKeyConverter
     */
    public function setDbKeyConverter(callable $converter = null): self
    {
    }

    /**
     * @param callable|null $converter
     * @return $this
     * @see UserModel::setPhpKeyConverter
     */
    public function setPhpKeyConverter(callable $converter = null): self
    {
    }

    /**
     * Set or remove cache time for the query
     *
     * @param int|null $seconds
     * @return $this
     * @see UserModel::setCacheTime
     */
    public function setCacheTime(?int $seconds): self
    {
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @see UserModel::unscoped
     */
    public function unscoped($scopes = []): self
    {
    }

    /**
     * Check if the model method defines the "Relation" attribute (or the "@Relation" tag in doc comment)
     *
     * This method only checks whether the specified method has the "Relation" attribute,
     * and does not check the actual logic.
     * It is provided for external use to avoid directly calling `$this->$relation()` to cause attacks.
     *
     * @param string $method
     * @return bool
     * @see UserModel::isRelation
     */
    public function isRelation(string $method): bool
    {
    }

    /**
     * @param array|string $columns
     * @return $this
     * @see UserModel::like
     */
    public function like($columns): self
    {
    }
}



}
namespace Wei;

if (0) {
class V
{
    /**
     * @return $this
     * @see \Miaoxing\Plugin\Service\IsModelExists::__invoke
     */
    public function modelExists($model = null, $column = 'id')
    {
    }

    /**
     * @return $this
     * @see \Miaoxing\Plugin\Service\IsModelExists::__invoke
     */
    public function notModelExists($model = null, $column = 'id')
    {
    }
}
}
