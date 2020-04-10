<?php

namespace Miaoxing\Plugin\Service;

class App extends \Wei\App
{
    /**
     * 根据域名查找应用名称
     *
     * @param string $domain
     * @return string|false
     * @see App::getIdByDomain
     */
    public function getIdByDomain($domain)
    {
    }
}

class AppModel extends Model
{
}

class Event extends \Wei\Event
{
    /**
     * @param string $name
     * @param array $args
     * @param bool $halt
     * @return array|mixed
     * @see Event::trig
     */
    public function trig($name, $args = [], $halt = false)
    {
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     * @see Event::trigUtil
     */
    public function trigUtil($name, $args = [])
    {
    }
}

class Model extends QueryBuilder
{
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
     * @param string|int $id
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
     * @return $this
     * @see Model::set
     */
    public function set($name, $value = null, $throwException = true)
    {
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string $id
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
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this|null
     * @see Model::findBy
     */
    public function findBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this|$this[]
     * @see Model::findAllBy
     */
    public function findAllBy($column, $operator = null, $value = null)
    {
    }

    /**
     * @param $attributes
     * @param array $data
     * @return $this
     * @see Model::findOrInitBy
     */
    public function findOrInitBy($attributes, $data = [])
    {
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param $column
     * @param $operator
     * @param mixed $value
     * @return $this
     * @throws \Exception
     * @see Model::findByOrFail
     */
    public function findByOrFail($column, $operator = null, $value = null)
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
     * @return $this
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
     * @param array|string $columns
     * @return $this
     * @see Model::like
     */
    public function like($columns)
    {
    }

    /**
     * @param string|array|true $scopes
     * @return $this
     * @see Model::unscoped
     */
    public function unscoped($scopes = [])
    {
    }
}

class Plugin extends \Miaoxing\Plugin\BaseService
{
}

class QueryBuilder extends \Wei\Base
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
     * @param mixed $conditions
     * @return array|null
     * @see QueryBuilder::fetch
     */
    public function fetch($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Executes the generated query and returns all array results
     *
     * @param mixed $conditions
     * @return array|false
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
     * @return array|null
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
     * @param null $value
     * @return int
     * @see QueryBuilder::update
     */
    public function update($set = [], $value = null)
    {
    }

    /**
     * Execute a delete query with specified conditions
     *
     * @param mixed $conditions
     * @return mixed
     * @see QueryBuilder::delete
     */
    public function delete($column = null, $operator = null, $value = null)
    {
    }

    /**
     * Sets the position of the first result to retrieve (the "offset")
     *
     * @param integer $offset The first result to return
     * @return $this
     * @see QueryBuilder::offset
     */
    public function offset($offset)
    {
    }

    /**
     * Sets the maximum number of results to retrieve (the "limit")
     *
     * @param integer $limit The maximum number of results to retrieve
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
     * @param string|array $columns The selection expressions.
     * @return $this
     * @see QueryBuilder::select
     */
    public function select($columns = ['*']): self
    {
    }

    /**
     * @param $columns
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
     * @param string|array $columns
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
     * @param string $type
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
     * @param string|array|Closure|null $column
     * @param null $operator
     * @param null $value
     * @return $this
     * @see QueryBuilder::where
     */
    public function where($column = null, $operator = null, $value = null)
    {
    }

    /**
     * @param string $expression
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereRaw
     */
    public function whereRaw($expression, $params = [])
    {
    }

    /**
     * @param $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereBetween
     */
    public function whereBetween($column, array $params)
    {
    }

    /**
     * @param $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotBetween
     */
    public function whereNotBetween($column, array $params)
    {
    }

    /**
     * @param $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereIn
     */
    public function whereIn($column, array $params)
    {
    }

    /**
     * @param $column
     * @param array $params
     * @return $this
     * @see QueryBuilder::whereNotIn
     */
    public function whereNotIn($column, array $params)
    {
    }

    /**
     * @param $column
     * @return $this
     * @see QueryBuilder::whereNull
     */
    public function whereNull($column)
    {
    }

    /**
     * @param $column
     * @return $this
     * @see QueryBuilder::whereNotNULL
     */
    public function whereNotNULL($column)
    {
    }

    /**
     * @param $column
     * @param $opOrValue
     * @param null $value
     * @return $this
     * @see QueryBuilder::whereDate
     */
    public function whereDate($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param $column
     * @param $opOrValue
     * @param null $value
     * @return $this
     * @see QueryBuilder::whereMonth
     */
    public function whereMonth($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param $column
     * @param $opOrValue
     * @param null $value
     * @return $this
     * @see QueryBuilder::whereDay
     */
    public function whereDay($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param $column
     * @param $opOrValue
     * @param null $value
     * @return $this
     * @see QueryBuilder::whereYear
     */
    public function whereYear($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param $column
     * @param $opOrValue
     * @param null $value
     * @return $this
     * @see QueryBuilder::whereTime
     */
    public function whereTime($column, $opOrValue, $value = null)
    {
    }

    /**
     * @param $column
     * @param $opOrColumn2
     * @param null $column2
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
     * @param $column
     * @param $value
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
     * @param mixed $column The grouping column.
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
     * @param string $conditions The having conditions
     * @param array $params The condition parameters
     * @param array $types The parameter types
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
     * @param string $column The ordering expression.
     * @param string $order The ordering direction.
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
     * @param string $lock
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
     * @param int|null|false $seconds
     * @return $this
     * @see QueryBuilder::cache
     */
    public function cache($seconds = null)
    {
    }
}

class Session extends \Wei\Session
{
}

class User extends UserModel
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
     * 根据用户账号密码,登录用户
     *
     * @param mixed $data
     * @return array
     * @see User::login
     */
    public function login($data)
    {
    }

    /**
     * 根据用户ID直接登录用户
     *
     * @param int $id
     * @return array
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
     * @return array
     * @see User::loginByModel
     */
    public function loginByModel(UserModel $user)
    {
    }

    /**
     * 销毁用户会话,退出登录
     *
     * @return $this
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
}

class UserModel extends Model
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
}
