<?php

namespace Miaoxing\Plugin;

use JsonSerializable;
use Miaoxing\Plugin\Model\DefaultScopeTrait;
use Wei\Logger;
use Wei\Record;
use Wei\RetTrait;

/**
 * @method \Miaoxing\Plugin\BaseModel db($table = null) Create a new record object
 * @property \Wei\BaseCache $cache
 * @property \Miaoxing\Plugin\Service\Plugin $plugin
 * @property Logger $logger
 * @SuppressWarnings(PHPMD.ExcessiveClassLength) 允许模型类较长
 */
class BaseModel extends Record implements JsonSerializable
{
    use RetTrait;
    use DefaultScopeTrait;

    protected $guarded = [
        //'id', 需要区分,象skuConfig表就要从外部设置id
        'appId',
        'createTime',
        'createUser',
        'updateTime',
        'updateUser',
    ];

    /**
     * 插入时是否自动生成递增的数字编号
     *
     * @var bool
     */
    protected $autoId = false;

    /**
     * 是否启用垃圾箱,启用后,删除的记录将会被转移到trash表
     *
     * @var bool
     */
    protected $enableTrash = false;

    /**
     * {@inheritdoc}
     */
    protected $defaultCacheTime = 1800;

    protected $appIdColumn = 'appId';

    protected $createdAtColumn = 'createTime';

    protected $updatedAtColumn = 'updateTime';

    protected $createdByColumn = 'createUser';

    protected $updatedByColumn = 'updateUser';

    protected $deletedAtColumn = 'deleteTime';

    protected $deletedByColumn = 'deleteUser';

    protected $userIdColumn = 'userId';

    /**
     * The relation configs
     *
     * @var array
     */
    protected $relations = [];

    /**
     * The relations have been loaded
     *
     * @var array
     */
    protected $loadedRelations = [];

    /**
     * The value for relation base query
     *
     * @var mixed
     */
    protected $relationValue;

    /**
     * @var array
     */
    protected $virtual = [];

    /**
     * @var bool
     */
    protected $enableProperty = false;

    /**
     * 返回数组时,通过get方法获取值
     *
     * @var bool
     */
    protected $toArrayV2 = false;

    /**
     * 是否自动识别出复数的表名
     *
     * @var bool
     */
    protected $tableV2 = false;

    /**
     * @var array
     */
    protected $hidden = [];

    protected static $snakeCache = [];

    protected static $camelCache = [];

    protected static $booted = [];

    protected static $events = [];

    protected $sqlParts = array(
        'select' => array(),
        'from' => null,
        'join' => array(),
        'set' => array(),
        'where' => null,
        'groupBy' => array(),
        'having' => null,
        'orderBy' => array(),
        'limit' => null,
        'offset' => null,
        'page' => null,
    );

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->boot();
    }

    public function boot()
    {
        $class = get_called_class();
        if (isset(static::$booted[$class])) {
            return;
        }

        static::$booted[$class] = true;
        foreach ($this->classUsesDeep($this) as $trait) {
            $parts = explode('\\', $trait);
            $method = 'boot' . array_pop($parts);
            if (method_exists($class, $method)) {
                $this->$method($this);
            }
        }
    }

    /**
     * @param $class
     * @param bool $autoload
     * @return array
     * @link http://php.net/manual/en/function.class-uses.php#110752
     */
    public function classUsesDeep($class, $autoload = true)
    {
        $traits = [];
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));
        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique($traits);
    }

    /**
     * @return BaseModel|BaseModel[]
     */
    public function __invoke()
    {
        if (!$this->table) {
            $this->detectTable();
        }
        $this->db->addRecordClass($this->table, get_class($this));

        return $this->db($this->table);
    }

    public function beforeCreate()
    {
        if ($this->autoId && !$this['id']) {
            $this['id'] = wei()->seq();
        }

        $fields = $this->getFields();

        if (in_array($this->createdAtColumn, $fields) && !$this[$this->createdAtColumn]) {
            $this[$this->createdAtColumn] = date('Y-m-d H:i:s');
        }

        if (in_array($this->createdByColumn, $fields) && !$this[$this->createdByColumn]) {
            $this[$this->createdByColumn] = (int) wei()->curUser['id'];
        }
    }

    public function beforeSave()
    {
        $fields = $this->getFields();

        if (in_array($this->updatedAtColumn, $fields)) {
            $this[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }

        if (in_array($this->updatedByColumn, $fields)) {
            $this[$this->updatedByColumn] = (int) wei()->curUser['id'];
        }
    }

    public function beforeDestroy()
    {
        if ($this->enableTrash) {
            $this->db->insert('trash', [
                'tableName' => $this->fullTable,
                'data' => json_encode($this->data),
                'deleteTime' => date('Y-m-d H:i:s'),
                'deleteUser' => (int) wei()->curUser['id'],
            ]);
        }
    }

    /**
     * QueryBuilder: 筛选属于当前登录用户的记录
     *
     * @return $this
     */
    public function mine()
    {
        return $this->andWhere([$this->userIdColumn => (int) wei()->curUser['id']]);
    }

    /**
     * QueryBuilder: 筛选属于当前应用的数据
     *
     * @return $this
     * @deprecated Use HasAppIdTrait
     */
    public function curApp()
    {
        $appId = wei()->app->getId();
        // 设置默认数据,初始化时就会带上
        $this->data[$this->appIdColumn] = $appId;

        return $this->andWhere([$this->appIdColumn => $appId]);
    }

    /**
     * Record: 设置当前应用ID
     *
     * @param int $appId
     * @return $this
     * @deprecated Use HasAppIdTrait
     */
    public function setAppId($appId = null)
    {
        return $this->set($this->appIdColumn, $appId ?: wei()->app->getId());
    }

    /**
     * QueryBuilder: 筛选未删除的数据
     *
     * @return $this
     * @deprecated Use softDelete trait
     */
    public function notDeleted()
    {
        return $this->andWhere([$this->fullTable . '.' . $this->deletedAtColumn => '0000-00-00 00:00:00']);
    }

    /**
     * QueryBuilder: 筛选已删除的数据
     *
     * @return $this
     * @deprecated Use softDelete trait
     */
    public function deleted()
    {
        return $this->andWhere($this->fullTable . '.' . $this->deletedAtColumn . " != '0000-00-00 00:00:00'");
    }

    /**
     * QueryBuilder: 筛选启用的数据
     *
     * @return $this
     */
    public function enabled()
    {
        return $this->andWhere(['enable' => true]);
    }

    /**
     * @return string
     * @todo 移到视图hepler?
     */
    public function getFormAction()
    {
        return $this->isNew ? 'create' : 'update';
    }

    public function getHttpMethod()
    {
        return $this->isNew ? 'POST' : 'PUT';
    }

    public function beColl()
    {
        $this->data = [];
        $this->isColl = true;

        return $this;
    }

    /**
     * 软删除
     *
     * @return $this
     * @deprecated Use softDelete trait
     */
    public function softDelete()
    {
        return $this->saveData([
            $this->deletedAtColumn => date('Y-m-d H:i:s'),
            $this->deletedByColumn => (int) wei()->curUser['id'],
        ]);
    }

    /**
     * Record: 检查该记录是否已经被删除了
     *
     * @return bool Use softDelete trait
     */
    public function isSoftDeleted()
    {
        return $this[$this->deletedAtColumn] && $this[$this->deletedAtColumn] != '0000-00-00 00:00:00';
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $data
     * @return $this
     */
    public function saveData($data = [])
    {
        $data && $this->setData($data);

        return $this->save();
    }

    /**
     * 如果提供了ID,检查该ID的数据是否存在,如果不存在则抛出404异常
     * 即FindOneOrInitById的缩写
     *
     * @param mixed $id
     * @param array|\ArrayAccess $data
     * @return $this
     * @todo 和已有的find方法可能会混淆,是否要改为seek或lookup
     */
    public function findId($id, $data = [])
    {
        if ($id) {
            $this->findOneById($id);
        } else {
            // Reset status when record not found
            $this->isNew = true;
        }

        return $this->fromArray($data);
    }

    public function findAllByIds($ids)
    {
        if (!$ids) {
            return $this->beColl();
        }

        return $this->findAll(['id' => $ids]);
    }

    /**
     * 设置缓存的标签为当前表名+用户ID
     *
     * @return $this
     */
    public function tagByUser()
    {
        return $this->tags($this->getUserTag());
    }

    /**
     * Record: 清除当前记录的缓存
     *
     * @return $this
     */
    public function clearRecordCache()
    {
        if ($this['id']) {
            $this->cache->remove($this->getRecordCacheKey());
        }

        return $this;
    }

    /**
     * Record: 获取当前记录的缓存键名
     *
     * @param int|null $id
     * @return string
     */
    public function getRecordCacheKey($id = null)
    {
        return $this->db->getDbname() . ':' . $this->table . ':' . ($id ?: $this['id']);
    }

    /**
     * @return $this
     */
    public function clearTagCacheByUser()
    {
        $tag = $this->getUserTag();
        $this->tagCache($tag)->clear();

        return $this;
    }

    /**
     * @return string
     */
    public function getUserTag()
    {
        return $this->table . ':' . ($this['userId'] ?: wei()->curUser['id']);
    }

    /**
     * 获取包含数据库名词的数据表,如app.user,方便跨库查询
     *
     * @param string $table
     * @return string
     */
    public function getDbTable($table)
    {
        return wei()->app->getDbName($this[$this->appIdColumn]) . '.' . $table;
    }

    /**
     * 将类名的最后一段作为数据表名称
     */
    protected function detectTable()
    {
        if (!$this->table) {
            // 适合类名: Miaoxing\Plugin\Service\User
            $parts = explode('\\', get_class($this));
            $basename = end($parts);

            // TODO V2 TODO plural
            $endWiths = substr($basename, -5) === 'Model';
            if ($this->tableV2 || $endWiths) {
                $endWiths && $basename = substr($basename, 0, -5);
                $this->table = $this->snake($basename) . 's';
            } else {
                $this->table = lcfirst($basename);
            }
        }
    }

    /**
     * Returns the record data
     *
     * @return $this[]|array
     */
    public function getData()
    {
        return $this->data;
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
     * Specifies an item that is not to be returned in the query result.
     * Replaces any previously specified selections, if any.
     *
     * @param string|array $fields
     * @return $this
     */
    public function selectExcept($fields)
    {
        $fields = array_diff($this->getFields(), is_array($fields) ? $fields : [$fields]);

        return $this->select($fields);
    }

    /**
     * {@inheritdoc}
     */
    protected function loadData($offset)
    {
        if (!$this->loaded && !$this->isNew) {
            if ($this->table !== 'user') {
                wei()->statsD->increment('record.loadData.' . $this->table);
            }
        }
        parent::loadData($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param \Closure $fn
     * @deprecated 使用filter
     */
    public function filterDeprecated(\Closure $fn)
    {
        $this->data = array_filter($this->data, $fn);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($returnFields = [])
    {
        if (!$this->isLoaded()) {
            $this->loadData($this->isColl() ? 0 : 'id');
        }

        if ($this->toArrayV2 && !$this->isColl) {
            $data = [];
            $columns = $this->getToArrayColumns($returnFields ?: $this->getFields());
            foreach ($columns as $column) {
                $data[$this->filterOutputColumn($column)] = $this->get($column);
            }

            return $data + $this->virtualToArray();
        }

        $result = parent::toArray($returnFields);
        if (!$this->toArrayV2) {
            $newResult = [];
            foreach ($result as $column => $value) {
                $newResult[$this->trigger('outputColumn', $column)] = $value;
            }

            return $newResult;
        }

        return $result;
    }

    protected function getToArrayColumns(array $columns)
    {
        if ($this->hidden) {
            $columns = array_diff($columns, $this->hidden);
        }

        return $columns;
    }

    public function setHidden($hidden)
    {
        $this->hidden = (array) $hidden;

        return $this;
    }

    protected function virtualToArray()
    {
        $data = [];
        foreach ($this->virtual as $column) {
            $data[$this->filterOutputColumn($column)] = $this->{'get' . $this->camel($column) . 'Attribute'}();
        }

        return $data;
    }

    /**
     * @param string $record
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return $this
     */
    public function hasOne($record, $foreignKey = null, $localKey = null)
    {
        $related = $this->getRelatedModel($record);
        $name = $related->getClassServiceName();

        $localKey || $localKey = $this->getPrimaryKey();
        $foreignKey || $foreignKey = $this->getForeignKey();
        $this->relations[$name] = ['foreignKey' => $foreignKey, 'localKey' => $localKey];

        $related->andWhere([$foreignKey => $this->getRelationValue($localKey)]);

        return $related;
    }

    /**
     * @param string $record
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return $this
     */
    public function hasMany($record, $foreignKey = null, $localKey = null)
    {
        return $this->hasOne($record, $foreignKey, $localKey)->beColl();
    }

    /**
     * @param string $record
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return BaseModel
     */
    public function belongsTo($record, $foreignKey = null, $localKey = null)
    {
        $related = $this->getRelatedModel($record);
        $foreignKey || $foreignKey = $this->getPrimaryKey();
        $localKey || $localKey = $this->snake($related->getClassServiceName()) . '_' . $this->getPrimaryKey();

        return $this->hasOne($related, $foreignKey, $localKey);
    }

    /**
     * @param string $record
     * @param string|null $junctionTable
     * @param string|null $foreignKey
     * @param string|null $relatedKey
     * @return BaseModel
     */
    public function belongsToMany($record, $junctionTable = null, $foreignKey = null, $relatedKey = null)
    {
        $related = $this->getRelatedModel($record);
        $name = $this->getClassServiceName($related);

        $primaryKey = $this->getPrimaryKey();
        $junctionTable || $junctionTable = $this->getJunctionTable($related);
        $foreignKey || $foreignKey = $this->getForeignKey();
        $relatedKey || $relatedKey = $this->snake($name) . '_' . $primaryKey;
        $this->relations[$name] = [
            'junctionTable' => $junctionTable,
            'relatedKey' => $relatedKey,
            'foreignKey' => $foreignKey,
            'localKey' => $primaryKey,
        ];

        $relatedTable = $related->getTable();
        $related->select($relatedTable . '.*')
            ->where([$junctionTable . '.' . $foreignKey => $this->getRelationValue($primaryKey)])
            ->innerJoin(
                $junctionTable,
                sprintf('%s.%s = %s.%s', $junctionTable, $relatedKey, $relatedTable, $primaryKey)
            )
            ->beColl();

        return $related;
    }

    /**
     * @param string|object $model
     * @return BaseModel
     */
    protected function getRelatedModel($model)
    {
        if ($model instanceof self) {
            return $model;
        } else {
            return $this->wei->$model();
        }
    }

    /**
     * Eager load relations
     *
     * @param string|array $names
     * @return $this|$this[]
     */
    public function load($names)
    {
        foreach ((array) $names as $name) {
            // 1. Load relation config
            $parts = explode('.', $name, 2);
            $name = $parts[0];
            $next = isset($parts[1]) ? $parts[1] : null;
            if (isset($this->loadedRelations[$name])) {
                continue;
            }

            /** @var BaseModel $related */
            $related = $this->$name();
            $isColl = $related->isColl();
            $serviceName = $this->getClassServiceName($related);
            $relation = $this->relations[$serviceName];

            // 2. Fetch relation record data
            $ids = $this->getAll($relation['localKey']);
            $ids = array_unique(array_filter($ids));
            if ($ids) {
                $this->relationValue = $ids;
                $related = $this->$name();
                $this->relationValue = null;
            } else {
                $related = null;
            }

            // 3. Load relation data
            if (isset($relation['junctionTable'])) {
                $records = $this->loadBelongsToMany($related, $relation, $name);
            } elseif ($isColl) {
                $records = $this->loadHasMany($related, $relation, $name);
            } else {
                $records = $this->loadHasOne($related, $relation, $name);
            }

            // 4. Load nested relations
            if ($next && $records) {
                $records->load($next);
            }

            $this->loadedRelations[$name] = true;
        }

        return $this;
    }

    protected function loadHasOne(Record $related = null, $relation, $name)
    {
        if ($related) {
            $records = $related->findAll()->indexBy($relation['foreignKey']);
        } else {
            $records = [];
        }
        foreach ($this->data as $row) {
            $row->$name = isset($records[$row[$relation['localKey']]]) ? $records[$row[$relation['localKey']]] : null;
        }

        return $records;
    }

    protected function loadHasMany(Record $related = null, $relation, $name)
    {
        $serviceName = $this->getClassServiceName($related);
        $records = $related ? $related->fetchAll() : [];
        foreach ($this->data as $row) {
            $rowRelation = $row->$name = $this->wei->$serviceName()->beColl();
            foreach ($records as $record) {
                if ($record[$relation['foreignKey']] == $row[$relation['localKey']]) {
                    $rowRelation[] = $this->wei->$serviceName()->fromArray($record);
                }
            }
        }

        return $records;
    }

    protected function loadBelongsToMany(Record $related = null, $relation, $name)
    {
        if ($related) {
            $related->addSelect($relation['junctionTable'] . '.' . $relation['foreignKey']);
        }

        return $this->loadHasMany($related, $relation, $name);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        // Receive service that conflict with record method name
        if (in_array($name, ['db', 'cache', 'lock', 'ret'])) {
            return parent::__get($name);
        }

        // Receive field value
        if ($this->enableProperty) {
            if ($this->hasColumn($name)) {
                return $this->get($name);
            }
        }

        // Receive relation
        if (method_exists($this, $name)) {
            return $this->getRelation($name);
        }

        // Receive service
        return parent::__get($name);
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

    protected function getClassServiceName($object = null)
    {
        !$object && $object = $this;
        $parts = explode('\\', get_class($object));
        $name = lcfirst(end($parts));

        // TODO deprecated
        if (substr($name, -6) == 'Record') {
            $name = substr($name, 0, -6);
        }

        if (substr($name, -5) == 'Model') {
            $name = substr($name, 0, -5);
        }

        return $name;
    }

    protected function getForeignKey()
    {
        return $this->snake($this->getClassServiceName($this)) . '_' . $this->getPrimaryKey();
    }

    protected function getJunctionTable(BaseModel $related)
    {
        $tables = [$this->getTable(), $related->getTable()];
        sort($tables);

        return implode('_', $tables);
    }

    protected function getRelationValue($field)
    {
        return $this->relationValue ?: $this->get($field);
    }

    /**
     * 直接设置表名，用于子查询的情况
     *
     * @param string $table
     * @return $this
     */
    public function setRawTable($table)
    {
        $this->table = $table;
        $this->fullTable = $this->db->getTable($this->table);

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function get($name)
    {
        $name = $this->filterInputColumn($name);

        $method = 'get' . $this->camel($name) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        $value = parent::get($name);

        return $this->trigger('getValue', [$value, $name]);
    }

    public function set($name, $value = null)
    {
        // Ignore $coll[] = $value
        if ($name !== null) {
            $name = $this->filterInputColumn($name);

            $method = 'set' . $this->camel($name) . 'Attribute';
            if (method_exists($this, $method)) {
                $this->setChanged($name);

                return $this->$method($value);
            }

            $value = $this->trigger('setValue', [$value, $name]);
        }

        return parent::set($name, $value);
    }

    public function hasColumn($name)
    {
        $name = $this->filterInputColumn($name);
        if (in_array($name, $this->getFields())) {
            return true;
        }

        $method = 'get' . $this->camel($name) . 'Attribute';

        return method_exists($this, $method);
    }

    public function isFillable($field)
    {
        if ($this->trigger('checkInputColumn', $field) === false) {
            return false;
        }

        return parent::isFillable($this->filterInputColumn($field));
    }

    protected function filterInputColumn($column)
    {
        return $this->trigger('inputColumn', $column);
    }

    protected function filterOutputColumn($column)
    {
        return $this->trigger('outputColumn', $column);
    }

    public function trigger($event, $data = [])
    {
        $result = null;
        $class = get_called_class();
        if (isset(static::$events[$class][$event])) {
            foreach (static::$events[$class][$event] as $callback) {
                // 优先使用自身方法
                if (method_exists($this, $callback)) {
                    $callback = [$this, $callback];
                }
                $result = call_user_func_array($callback, (array) $data);
            }
        } else {
            $result = is_array($data) ? current($data) : $data;
        }

        return $result;
    }

    public static function on($event, $method)
    {
        static::$events[get_called_class()][$event][] = $method;
    }

    public function execute()
    {
        $this->trigger('preExecute');

        return parent::execute();
    }

    public function add($sqlPartName, $sqlPart, $append = false, $type = null)
    {
        $this->trigger('preBuildQuery', func_get_args());

        return parent::add($sqlPartName, $sqlPart, $append, $type);
    }

    public function triggerCallback($name)
    {
        $this->trigger($name);
        parent::triggerCallback($name);
    }

    /**
     * 设置原生数据,如从数据库读出的数据
     *
     * @param array $data
     * @return BaseModel
     */
    public function setRawData(array $data)
    {
        $this->data = $data + $this->data;

        if ($data) {
            $this->loaded = true;
        }

        return $this;
    }

    protected function setChanged($name)
    {
        $this->changedData[$name] = isset($this->data[$name]) ? $this->data[$name] : null;
        $this->isChanged = true;
    }

    public function page($page)
    {
        $page = max(1, (int) $page);
        $this->add('page', $page);

        return parent::page($page);
    }

    public function limit($limit)
    {
        parent::limit($limit);

        // 计算出新的offset
        if ($page = $this->getSqlPart('page')) {
            $this->page($page);
        }

        return $this;
    }

    protected function getRelation($name)
    {
        /** @var BaseModel $related */
        $related = $this->$name();
        $serviceName = $this->getClassServiceName($related);
        $relation = $this->relations[$serviceName];
        $localValue = $this[$relation['localKey']];

        if ($related->isColl()) {
            if ($localValue) {
                $this->$name = $related->findAll();
            } else {
                $this->$name = $related;
            }
        } else {
            if ($localValue) {
                $this->$name = $related->find() ?: null;
            } else {
                $this->$name = null;
            }
        }

        return $this->$name;
    }
}
