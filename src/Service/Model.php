<?php

namespace Miaoxing\Plugin\Service;

use Closure;
use InvalidArgumentException;
use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Model\CamelCaseTrait;
use Miaoxing\Plugin\Model\CastTrait;
use Miaoxing\Plugin\Model\DefaultScopeTrait;
use Miaoxing\Services\Service\Request;
use Wei\Record;
use Wei\RetTrait;

/**
 * @mixin \UserMixin
 */
class Model extends QueryBuilder implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    use CamelCaseTrait;
    use CastTrait;
    use RetTrait;
    use DefaultScopeTrait;

    protected $appIdColumn = 'app_id';

    protected $createdAtColumn = 'created_at';

    protected $createdByColumn = 'created_by';

    protected $updatedAtColumn = 'updated_at';

    protected $updatedByColumn = 'updated_by';

    protected $deletedByColumn = 'deleted_by';

    protected $deletedAtColumn = 'deleted_at';

    protected $userIdColumn = 'user_id';

    protected $guarded = [
        'id',
        'app_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    /**
     * Whether it's a new record and has not save to database
     *
     * @var bool
     */
    protected $isNew = true;

    /**
     * The record data
     *
     * @var []|array
     */
    protected $data = [];

    /**
     * The fields that are assignable through fromArray method
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The fields that aren't assignable through fromArray method
     *
     * @var array
     */
    //protected $guarded = array('id');

    /**
     * Whether the record's data is changed
     *
     * @var bool
     */
    protected $isChanged = false;

    /**
     * The record data before changed
     *
     * @var array
     */
    protected $changedData = [];

    /**
     * Whether the record has been removed from database
     *
     * @var bool
     */
    protected $isDestroyed = false;

    /**
     * Whether the record is waiting to remove from database
     *
     * @var bool
     */
    protected $detached = false;

    /**
     * Whether the data is loaded
     *
     * @var bool
     */
    protected $loaded = false;

    /**
     * Whether it contains multiple or single row data
     *
     * @var bool
     */
    protected $isColl = false;

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
     * @var array
     */
    protected $relationValues = [];

    /**
     * The value for relation base query
     *
     * @var mixed
     */
    protected $relatedValue;

    /**
     * @var array
     */
    protected $virtual = [];

    /**
     * @var array
     */
    protected $hidden = [];

    protected static $booted = [];

    protected static $events = [];

    /**
     * @var array
     */
    protected $requiredServices = [
        'db',
        'cache',
        'logger',
        'ret',
        'str',
    ];

    protected $defaultValues = [
        'date' => '0000-00-00',
        'datetime' => '0000-00-00 00:00:00',
    ];

    /**
     * 数组来源
     *
     * php：数据经过代码处理，例如默认值
     * db：数据来自数据库，是一个未解码/未转换类型的字符串。如果经过处理，则变成php
     * user：数据来自用户设置
     *
     * @var array
     */
    protected $dataSources = [
        '*' => 'php',
    ];

    /**
     * @var array
     */
    protected $virtualData = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        if (isset($options['isNew']) && false === $options['isNew']) {
            $this->setRawData($options['data']);
            unset($options['data']);
        }

        parent::__construct($options);

        // Clear changed status after created
        $this->changedData = [];
        $this->isChanged = false;

        $this->triggerCallback('afterLoad');

        $this->boot();
    }

    /**
     * @return $this|$this[]
     */
    public function __invoke(string $table = null)
    {
        $this->db->addRecordClass($this->getTable(), static::class);

        return $this->db($this->table);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value = null)
    {
        // Required services first
        if (in_array($name, $this->requiredServices, true)) {
            return $this->{$name} = $value;
        }

        $result = $this->set($name, $value, false);
        if ($result) {
            return;
        }

        if ($this->wei->has($name)) {
            return $this->{$name} = $value;
        }

        throw new InvalidArgumentException('Invalid property: ' . $name);
    }

    /**
     * Create a new model object
     *
     * @param array $data
     * @param array $options
     * @return $this
     */
    public static function new($data = [], array $options = [])
    {
        $class = static::getServiceClass();
        return new $class($options + ['data' => $data]);
    }

    /**
     * @param array $data
     * @return $this|$this[]
     * @todo 待 data 改为 default ？ 后移除
     */
    public static function newColl($data = [])
    {
        $class = static::getServiceClass();
        return (new $class())->beColl()->fromArray($data);
    }

    /**
     * Returns the record data as array
     *
     * @param array $returnFields A indexed array specified the fields to return
     * @param callable|null $prepend
     * @return array
     */
    public function toArray($returnFields = [], callable $prepend = null)
    {
        if (!$this->isLoaded()) {
            $this->loadData($this->isColl() ? 0 : 'id');
        }

        if (!$this->isColl) {
            $data = [];
            $columns = $this->getToArrayColumns($returnFields ?: $this->getFields());
            foreach ($columns as $column) {
                $data[$this->convertOutputIdentifier($column)] = $this->get($column);
            }

            return $data + $this->virtualToArray() + $this->relationToArray();
        } else {
            if (is_callable($returnFields)) {
                $prepend = $returnFields;
                $returnFields = [];
            }

            $data = [];
            /** @var $record Record */
            foreach ($this->data as $key => $record) {
                $data[$key] = $record->toArray($returnFields);
                if ($prepend) {
                    $data[$key] = $prepend($record) + $data[$key];
                }
            }
            return $data;
        }
    }

    /**
     * Returns the record and relative records data as JSON string
     *
     * @param array $returnFields A indexed array specified the fields to return
     * @return string
     */
    public function toJson($returnFields = [])
    {
        return json_encode($this->toArray($returnFields));
    }

    /**
     * Set guarded columns
     *
     * @param array $guarded
     * @return $this
     */
    public function setGuarded(array $guarded)
    {
        $this->guarded = $guarded;
        return $this;
    }

    /**
     * Set fillable columns
     *
     * @param array $fillable
     * @return $this
     */
    public function setFillable(array $fillable)
    {
        $this->fillable = $fillable;
        return $this;
    }

    /**
     * Check if the field is assignable through fromArray method
     *
     * @param string $field
     * @param mixed|null $data
     * @return bool
     */
    public function isFillable($field, $data = null)
    {
        if (false === $this->trigger('checkInputColumn', [$field, $data])) {
            return false;
        }

        $field = $this->convertInputIdentifier($field);

        return !in_array($field, $this->guarded, true) && !$this->fillable || in_array($field, $this->fillable, true);
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     */
    public function setData($data)
    {
        foreach ($data as $field => $value) {
            $this->set($field, $value);
        }
        return $this;
    }

    /**
     * Reload the record data from database
     *
     * @return $this
     */
    public function reload()
    {
        $this->dataSources = ['*' => 'db'];

        $this->data = (array) $this->db->select(
            $this->table,
            [$this->primaryKey => $this->get($this->primaryKey)]
        );
        $this->changedData = [];
        $this->isChanged = false;
        $this->triggerCallback('afterLoad');
        return $this;
    }

    /**
     * Merges data into collection and save to database, including insert, update and delete
     *
     * @param array $data A two-dimensional array
     * @param array $extraData The extra data for new rows
     * @param bool $sort
     * @return $this
     */
    public function saveColl($data, $extraData = [], $sort = false)
    {
        if (!is_array($data)) {
            return $this;
        }

        // 1. Uses primary key as data index
        $newData = [];
        foreach ($this->data as $key => $record) {
            unset($this->data[$key]);
            // Ignore default data
            if ($record instanceof $this) {
                $newData[$record[$this->primaryKey]] = $record;
            }
        }
        $this->data = $newData;

        // 2. Removes empty rows from data
        foreach ($data as $index => $row) {
            if (!array_filter($row)) {
                unset($data[$index]);
            }
        }

        // 3. Removes missing rows
        $existIds = [];
        foreach ($data as $row) {
            if (isset($row[$this->primaryKey]) && null !== $row[$this->primaryKey]) {
                $existIds[] = $row[$this->primaryKey];
            }
        }
        /** @var $record Record */
        foreach ($this->data as $key => $record) {
            if (!in_array($record[$this->primaryKey], $existIds, true)) {
                $record->destroy();
                unset($this->data[$key]);
            }
        }

        // 4. Merges existing rows or create new rows
        foreach ($data as $index => $row) {
            if ($sort) {
                $row[$sort] = $index;
            }
            if (isset($row[$this->primaryKey], $this->data[$row[$this->primaryKey]])) {
                $this->data[$row[$this->primaryKey]]->fromArray($row);
            } else {
                $this[] = $this->db($this->table)->fromArray($extraData + $row);
            }
        }

        // 5. Save and return
        return $this->save();
    }

    /**
     * Receives the record field value
     *
     * @param string $name
     * @param mixed|null $exists
     * @param mixed $throwException
     * @return $this|mixed
     * @throws InvalidArgumentException When field not found
     */
    public function &get($name, &$exists = null, $throwException = true)
    {
        $exists = true;

        // Receive field value
        if ($this->isCollKey($name) || $this->hasColumn($name) || array_key_exists($name, $this->data)) {
            return $this->getColumnValue($name);
        }

        // Receive virtual column value
        if ($this->hasVirtual($name)) {
            return $this->getVirtualValue($name);
        }

        // Receive relation
        if ($this->hasRelation($name)) {
            return $this->getRelationValue($name);
        }

        $exists = false;
        if ($throwException) {
            throw new InvalidArgumentException('Invalid property: ' . $name);
        } else {
            $null = null;
            return $null;
        }
    }

    /**
     * Receives the record field value
     *
     * @param string $name
     * @return $this|mixed
     * @throws InvalidArgumentException When field not found
     */
    public function origGet($name)
    {
        // Check if field exists when it is not a collection
        if (!$this->isColl && !in_array($name, $this->getFields(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Field "%s" not found in record class "%s"',
                $name,
                static::class
            ));
        }
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throws InvalidArgumentException
     */
    public function origSet($name, $value = null)
    {
        $this->loaded = true;

        // Set record for collection
        if (!$this->data && $value instanceof static) {
            $this->isColl = true;
        }

        if (!$this->isColl) {
            if (in_array($name, $this->getFields(), true)) {
                $this->changedData[$name] = isset($this->data[$name]) ? $this->data[$name] : null;
                $this->data[$name] = $value;
                $this->isChanged = true;
            }
        } else {
            if (!$value instanceof static) {
                throw new InvalidArgumentException('Value for collection must be an instance of Wei\Record');
            } else {
                // Support $coll[] = $value;
                if (null === $name) {
                    $this->data[] = $value;
                } else {
                    $this->data[$name] = $value;
                }
            }
        }
        return $this;
    }

    /**
     * Set field value for every record in collection
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setAll($name, $value)
    {
        foreach ($this->data as $record) {
            $record[$name] = $value;
        }
        return $this;
    }

    /**
     * Return the value of field from every record in collection
     *
     * @param string $name
     * @return array
     */
    public function getAll($name)
    {
        $data = [];
        foreach ($this->data as $record) {
            $data[] = $record[$name];
        }
        return $data;
    }

    /**
     * Remove field value
     *
     * @param string $name The name of field
     * @return $this
     */
    public function remove($name)
    {
        $name = $this->convertInputIdentifier($name);

        if (!$this->isColl) {
            if (array_key_exists($name, $this->data)) {
                $this->data[$name] = null;
            }
        } else {
            unset($this->data[$name]);
        }
        return $this;
    }

    /**
     * Increment a field
     *
     * @param string $name
     * @param int $offset
     * @return $this
     */
    public function incr($name, $offset = 1)
    {
        $name = $this->convertInputIdentifier($name);
        $this[$name] = (object) ($name . ' + ' . $offset);
        return $this;
    }

    /**
     * Decrement a field
     *
     * @param string $name
     * @param int $offset
     * @return $this
     */
    public function decr($name, $offset = 1)
    {
        $name = $this->convertInputIdentifier($name);
        $this[$name] = (object) ($name . ' - ' . $offset);
        return $this;
    }

    /**
     * Set the detach status for current record
     *
     * @param bool $bool
     * @return $this
     */
    public function detach($bool = true)
    {
        $this->detached = (bool) $bool;
        return $this;
    }

    /**
     * Check if it's a new record and has not save to database
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * Check if the record has been removed from the database
     *
     * @return bool
     */
    public function isDestroyed()
    {
        return $this->isDestroyed;
    }

    /**
     * Check if the record is waiting to remove from database
     *
     * @return bool
     */
    public function isDetached()
    {
        return $this->detached;
    }

    public function isColl()
    {
        return $this->isColl;
    }

    /**
     * Returns whether the data is loaded
     *
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }

    /**
     * Return the field data before changed
     *
     * @param string $field
     * @return array|string
     */
    public function getChangedData($field = null)
    {
        if ($field) {
            return isset($this->changedData[$field]) ? $this->changedData[$field] : null;
        }
        return $this->changedData;
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @deprecated use findOrFail
     */
    public function findOne($id)
    {
        return $this->findOrFail($id);
    }

    /**
     * Check if the offset exists
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $this->loadData($offset);
        return isset($this->data[$offset]);
    }

    /**
     * Get the offset value
     *
     * @param string $offset
     * @return mixed
     */
    public function &offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set the offset value
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->loadData($offset);
        $this->set($offset, $value);
    }

    /**
     * Unset the offset
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->loadData($offset);
        $this->remove($offset);
    }

    /**
     * Retrieve an array iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->loadData(0);
        return new \ArrayIterator($this->data);
    }

    /**
     * Filters elements of the collection using a callback function
     *
     * @param Closure $fn
     * @return $this|$this[]
     */
    public function filter(Closure $fn)
    {
        $data = array_filter($this->data, $fn);
        $records = $this->db->init($this->table, [], $this->isNew);
        $records->data = $data;
        $records->isColl = true;
        $records->loaded = $this->loaded;
        return $records;
    }

    /**
     * The method called after load a record
     */
    public function afterLoad()
    {
    }

    /**
     * The method called after find a record
     */
    public function afterFind()
    {
    }

    /**
     * The method called before save a record
     */
    public function beforeSave()
    {
        $fields = $this->getFields();

        if (in_array($this->updatedAtColumn, $fields, true)) {
            $this[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }

        if (in_array($this->updatedByColumn, $fields, true)) {
            $this[$this->updatedByColumn] = (int) $this->user->id;
        }
    }

    /**
     * The method called after save a record
     */
    public function afterSave()
    {
    }

    /**
     * The method called before insert a record
     */
    public function beforeCreate()
    {
        $fields = $this->getFields();

        if (in_array($this->createdAtColumn, $fields, true) && !$this[$this->createdAtColumn]) {
            $this[$this->createdAtColumn] = date('Y-m-d H:i:s');
        }

        if (in_array($this->createdByColumn, $fields, true) && !$this[$this->createdByColumn]) {
            $this[$this->createdByColumn] = (int) $this->user->id;
        }
    }

    /**
     * The method called after insert a record
     */
    public function afterCreate()
    {
    }

    /**
     * The method called before update a record
     */
    public function beforeUpdate()
    {
    }

    /**
     * The method called after update a record
     */
    public function afterUpdate()
    {
    }

    /**
     * The method called before delete a record
     */
    public function beforeDestroy()
    {
    }

    /**
     * The method called after delete a record
     */
    public function afterDestroy()
    {
    }

    public function boot()
    {
        $class = static::class;
        if (isset(static::$booted[$class])) {
            return;
        }

        static::$booted[$class] = true;
        foreach ($this->classUsesDeep($this) as $trait) {
            $parts = explode('\\', $trait);
            $method = 'boot' . array_pop($parts);
            if (method_exists($class, $method)) {
                $this->{$method}($this);
            }
        }
    }

    /**
     * @param $class
     * @param bool $autoload
     * @return array
     * @see http://php.net/manual/en/function.class-uses.php#110752
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
     * @return $this|$this[]
     */
    public function beColl()
    {
        $this->data = [];
        $this->isColl = true;

        return $this;
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
        return $this->db->getDbname() . ':' . $this->getTable() . ':' . ($id ?: $this['id']);
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
        return $this->table . ':' . ($this['userId'] ?: $this->user->id);
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
     * Returns the record data
     *
     * @return $this[]|array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function setHidden($hidden)
    {
        $this->hidden = (array) $hidden;

        return $this;
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

        $related->where($foreignKey, $this->getRelatedValue($localKey));

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
     * @return $this
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
     * @return $this
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
            ->where([$junctionTable . '.' . $foreignKey => $this->getRelatedValue($primaryKey)])
            ->innerJoin($junctionTable, $junctionTable . '.' . $relatedKey, '=', $relatedTable . '.' . $primaryKey)
            ->beColl();

        return $related;
    }

    /**
     * Eager load relations
     *
     * @param array|string $names
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

            /** @var static $related */
            $related = $this->{$name}();
            $isColl = $related->isColl();
            $serviceName = $this->getClassServiceName($related);
            $relation = $this->relations[$serviceName];

            // 2. Fetch relation record data
            $ids = $this->getAll($relation['localKey']);
            $ids = array_unique(array_filter($ids));
            if ($ids) {
                $this->relatedValue = $ids;
                $related = $this->{$name}();
                $this->relatedValue = null;
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

    /**
     * {@inheritdoc}
     */
    public function &__get($name)
    {
        // Receive service that conflict with record method name
        if (in_array($name, $this->requiredServices, true)) {
            return $this->getServiceValue($name);
        }

        $value = &$this->get($name, $exists, false);
        if ($exists) {
            return $value;
        }

        // Receive other services
        return $this->getServiceValue($name);
    }

    /**
     * Check if column name exists
     *
     * @param string $name
     * @return bool
     */
    public function hasColumn($name)
    {
        $name = $this->convertInputIdentifier($name);

        return in_array($name, $this->getFields(), true);
    }

    public function trigger($event, $data = [])
    {
        $result = null;
        $class = static::class;
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
        static::$events[static::class][$event][] = $method;
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

    /**
     * Check if the record's data or specified field is changed
     *
     * @param string $field
     * @return bool
     */
    public function isChanged($field = null)
    {
        if ($field) {
            $field = $this->convertInputIdentifier($field);
            return array_key_exists($field, $this->changedData);
        }
        return $this->isChanged;
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return array
     */
    public function toRet(array $merge = [])
    {
        if ($this->isColl()) {
            return $this->suc($merge + [
                    'data' => $this,
                    'page' => $this->getSqlPart('page'),
                    'rows' => $this->getSqlPart('limit'),
                    'records' => $this->count(),
                ]);
        } else {
            return $this->suc($merge + ['data' => $this]);
        }
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return $this
     */
    public function setRawValue($column, $value)
    {
        $this->data[$column] = $value;
        return $this;
    }

    public function incrSave($name, $offset = 1)
    {
        $value = $this->get($name) + $offset;
        $this->incr($name, $offset)->save();
        $this->set($name, $value);
        $this->resetChanged($name);

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function existOrFail()
    {
        if ($this->isNew) {
            throw new \Exception('Record not found', 404);
        }
        return $this;
    }

    /**
     * Returns the record number in collection
     *
     * @return int
     */
    public function count()
    {
        $this->loadData(0);
        return count($this->data);
    }

    public function selectMain($column = '*')
    {
        return $this->select($this->getTable() . '.' . $column);
    }

    /**
     * 搜索某一列是否有值
     *
     * @param string $column
     * @param bool $value
     * @return $this
     */
    public function whereHas($column, $value = true)
    {
        if (isset($this->defaultValues[$this->casts[$column]])) {
            $default = $this->defaultValues[$this->casts[$column]];
        } else {
            $default = '';
        }
        $op = $value ? '!=' : '=';
        $this->where($column . ' ' . $op . ' \'' . $default . '\'');

        return $this;
    }

    /**
     * Set raw data to model
     *
     * @param array $data
     * @return $this
     */
    public function setRawData(array $data)
    {
        $this->data = $data + $this->data;

        if ($data) {
            $this->loaded = true;
            $this->setDataSource('*', 'db');
        }

        return $this;
    }

    /**
     * Return the record table name
     *
     * @return string
     * @svc
     */
    protected function getTable()
    {
        if (!$this->table) {
            $baseName = $this->baseName();
            if ('Model' === substr($baseName, -5)) {
                $baseName = substr($baseName, 0, -5);
            }
            $this->table = $this->pluralize($this->snake($baseName));
        }
        return $this->table;
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     * @svc
     */
    protected function fromArray($data)
    {
        foreach ($data as $key => $value) {
            if (is_int($key) || $this->isFillable($key, $data)) {
                $this->set($key, $value, false);
            }
        }
        return $this;
    }

    /**
     * Save the record or data to database
     *
     * @param array $data
     * @return $this
     * @svc
     */
    protected function save($data = [])
    {
        // 1. Merges data from parameters
        $data && $this->fromArray($data);

        // 将数据转换为数据库数据
        $origData = $this->data;
        $this->data = $this->generateDbData();
        $isNew = $this->isNew;

        // 2.1 Saves single record
        if (!$this->isColl) {
            // 2.1.1 Returns when record has been destroy to avoid store dirty data
            if ($this->isDestroyed) {
                return $this;
            }

            // Deletes the record when it's waiting to remove from database
            if ($this->detached) {
                $this->db->delete($this->getTable(), [$this->primaryKey => $this->data[$this->primaryKey]]);
                $this->isDestroyed = true;
                return $this;
            }

            // 2.1.2 Triggers before callbacks
            $isNew = $this->isNew;
            $this->triggerCallback('beforeSave');
            $this->triggerCallback($isNew ? 'beforeCreate' : 'beforeUpdate');

            // 2.1.3.1 Inserts new record
            if ($isNew) {
                // Removes primary key value when it's empty to avoid SQL error
                if (array_key_exists($this->primaryKey, $this->data) && !$this->data[$this->primaryKey]) {
                    unset($this->data[$this->primaryKey]);
                }

                $this->db->insert($this->getTable(), $this->data);
                $this->isNew = false;

                // Receives primary key value when it's empty
                if (!isset($this->data[$this->primaryKey]) || !$this->data[$this->primaryKey]) {
                    // Prepare sequence name for PostgreSQL
                    $sequence = sprintf('%s_%s_seq', $this->db->getTable($this->getTable()), $this->primaryKey);
                    $this->data[$this->primaryKey] = $this->db->lastInsertId($sequence);
                }
                // 2.1.3.2 Updates existing record
            } else {
                if ($this->isChanged) {
                    $data = array_intersect_key($this->data, $this->changedData);
                    $this->db->update($this->getTable(), $data, [
                        $this->primaryKey => $this->data[$this->primaryKey],
                    ]);
                }
            }

            // 2.1.4 Reset changed data and changed status
            $this->changedData = [];
            $this->isChanged = false;

            // 2.1.5. Triggers after callbacks
            $this->triggerCallback($isNew ? 'afterCreate' : 'afterUpdate');
            $this->triggerCallback('afterSave');
        // 2.2 Loop and save collection records
        } else {
            foreach ($this->data as $record) {
                $record->save();
            }
        }

        if ($isNew) {
            $this->setDataSource($this->primaryKey, 'db');
        }

        // 解决保存之前调用了$this->id导致变为null的问题
        if ($isNew && array_key_exists($this->primaryKey, $origData)) {
            $origData[$this->primaryKey] = $this->data[$this->primaryKey];
        }

        // 还原原来的数据+save过程中生成的主键数据
        $this->data = $origData + $this->data;

        return $this;
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @svc
     */
    protected function destroy($id = null)
    {
        $id && $this->find($id);
        !$this->loaded && $this->loadData(0);

        if (!$this->isColl) {
            $this->triggerCallback('beforeDestroy');
            $this->executeDestroy();
            $this->isDestroyed = true;
            $this->triggerCallback('afterDestroy');
        } else {
            foreach ($this->data as $record) {
                $record->destroy();
            }
        }

        return $this;
    }

    protected function executeDestroy()
    {
        $this->db->delete($this->getTable(), [$this->primaryKey => $this->data[$this->primaryKey]]);
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this
     * @svc
     */
    protected function set($name, $value = null, $throwException = true)
    {
        if ($this->isCollKey($name) || $this->hasColumn($name)) {
            return $this->setColumnValue($name, $value);
        }

        if ($this->hasVirtual($name)) {
            return $this->setVirtualValue($name, $value);
        }

        if ($this->hasRelation($name)) {
            return $this->setRelationValue($name, $value);
        }

        if ($throwException) {
            throw new InvalidArgumentException('Invalid property: ' . $name);
        } else {
            return false;
        }
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param int|string $id
     * @return $this|null
     * @svc
     */
    protected function find($id)
    {
        return $this->findBy($this->primaryKey, $id);
    }

    /**
     * Find a record by primary key, or throws 404 exception if record not found
     *
     * @param int|string $id
     * @return $this
     * @throws \Exception
     * @svc
     */
    protected function findOrFail($id)
    {
        if ($this->find($id)) {
            return $this;
        } else {
            throw new \Exception('Record not found', 404);
        }
    }

    /**
     * Find a record by primary key, or init with the specified data if record not found
     *
     * @param int|string $id
     * @param array|object $data
     * @return $this
     * @svc
     */
    protected function findOrInit($id = null, $data = [])
    {
        return $this->findOrInitBy([$this->primaryKey => $id], $data);
    }

    /**
     * Find a record by primary key, or save with the specified data if record not found
     *
     * @param int|string $id
     * @param array $data
     * @return $this
     * @svc
     */
    protected function findOrCreate($id, $data = [])
    {
        $this->findOrInit($id, $data);
        if ($this->isNew) {
            $this->save();
        }
        return $this;
    }

    /**
     * @param $attributes
     * @param array $data
     * @return $this
     * @svc
     */
    protected function findByOrCreate($attributes, $data = [])
    {
        return $this->findOrInitBy($attributes, $data)->save();
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param array $ids
     * @return $this|$this[]
     * @svc
     */
    protected function findAll($ids)
    {
        return $this->findAllBy($this->primaryKey, 'IN', $ids);
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this|null
     * @svc
     */
    protected function findBy($column, $operator = null, $value = null)
    {
        $this->isColl = false;
        $data = $this->fetch(...func_get_args());
        if ($data) {
            $this->setDataSource('*', 'db');
            $this->data = $data + $this->data;
            $this->triggerCallback('afterFind');
            return $this;
        } else {
            return null;
        }
    }

    /**
     * @param $column
     * @param null $operator
     * @param null $value
     * @return $this|$this[]
     * @svc
     */
    protected function findAllBy($column, $operator = null, $value = null)
    {
        $this->isColl = true;
        $data = $this->fetchAll(...func_get_args());

        $records = [];
        foreach ($data as $key => $row) {
            /** @var $records Record[] */
            $records[$key] = static::new($row, [
                'wei' => $this->wei,
                'db' => $this->db,
                'table' => $this->getTable(),
                'isNew' => false,
            ]);
            $records[$key]->triggerCallback('afterFind');
        }

        $this->data = $records;
        return $this;
    }

    /**
     * @param $attributes
     * @param array $data
     * @return $this
     * @svc
     */
    protected function findOrInitBy($attributes, $data = [])
    {
        if (!$this->findBy($attributes)) {
            // Reset status when record not found
            $this->isNew = true;

            // Convert to object to array
            if (is_object($data) && method_exists($data, 'toArray')) {
                $data = $data->toArray();
            }

            $this->setData($attributes);
            $this->fromArray($data);
        }
        return $this;
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param $column
     * @param $operator
     * @param mixed $value
     * @return $this
     * @throws \Exception
     * @svc
     */
    protected function findByOrFail($column, $operator = null, $value = null)
    {
        if ($this->findBy(...func_get_args())) {
            return $this;
        } else {
            throw new \Exception('Record not found', 404);
        }
    }

    /**
     * @param array|Request|null $request
     * @return $this
     * @throws \Exception
     * @svc
     */
    protected function findFromRequest($request = null)
    {
        $request || $request = $this->wei->request;
        if (in_array($request['action'], ['edit', 'update'], true)) {
            $this->findOrFail($request[$this->getPrimaryKey()]);
        }
        return $this;
    }

    /**
     * Executes the generated SQL and returns the found record object or null if not found
     *
     * @return $this|null
     * @svc
     */
    protected function first()
    {
        return $this->findBy(null);
    }

    /**
     * @return $this
     * @svc
     */
    protected function all()
    {
        return $this->findAllBy(null);
    }

    /**
     * Load record by array offset
     *
     * @param int|string $offset
     */
    protected function loadData($offset)
    {
        if (!$this->loaded && !$this->isNew) {
            if (is_numeric($offset) || null === $offset) {
                $this->all();
            } else {
                $this->first();
            }
        }
    }

    /**
     * Trigger a callback
     *
     * @param string $name
     */
    protected function triggerCallback($name)
    {
        $this->trigger($name);
        $this->{$name}();
    }

    protected function getToArrayColumns(array $columns)
    {
        if ($this->hidden) {
            $columns = array_diff($columns, $this->hidden);
        }

        return $columns;
    }

    protected function virtualToArray()
    {
        $data = [];
        foreach ($this->virtual as $column) {
            $data[$this->convertOutputIdentifier($column)] = $this->{'get' . $this->camel($column) . 'Attribute'}();
        }

        return $data;
    }

    protected function relationToArray()
    {
        $data = [];
        foreach ($this->relationValues as $name => $value) {
            $data[$name] = $value ? $value->toArray() : null;
        }
        return $data;
    }

    /**
     * @param object|string $model
     * @return $this
     */
    protected function getRelatedModel($model)
    {
        if ($model instanceof self) {
            return $model;
        } elseif (is_subclass_of($model, self::class)) {
            return forward_static_call([$model, 'new']);
        } else {
            return $this->wei->{$model}();
        }
    }

    protected function loadHasOne(self $related, $relation, $name)
    {
        if ($related) {
            $records = $related->all()->indexBy($relation['foreignKey']);
        } else {
            $records = [];
        }
        foreach ($this->data as $row) {
            $row->{$name} = isset($records[$row[$relation['localKey']]]) ? $records[$row[$relation['localKey']]] : null;
        }

        return $records;
    }

    protected function loadHasMany(self $related, $relation, $name)
    {
        $records = $related ? $related->fetchAll() : [];
        foreach ($this->data as $row) {
            $rowRelation = $row->{$name} = $related::newColl();
            foreach ($records as $record) {
                if ($record[$relation['foreignKey']] === $row[$relation['localKey']]) {
                    // Remove external data
                    if (!$related->hasColumn($relation['foreignKey'])) {
                        unset($record[$relation['foreignKey']]);
                    }
                    $rowRelation[] = forward_static_call([$related, 'new'])->setData($record);
                }
            }
        }

        return $records;
    }

    protected function loadBelongsToMany(self $related, $relation, $name)
    {
        if ($related) {
            $related->select($relation['junctionTable'] . '.' . $relation['foreignKey']);
        }

        return $this->loadHasMany($related, $relation, $name);
    }

    protected function getClassServiceName($object = null)
    {
        !$object && $object = $this;
        $parts = explode('\\', get_class($object));
        $name = lcfirst(end($parts));

        if ('Model' == substr($name, -5)) {
            $name = substr($name, 0, -5);
        }

        return $name;
    }

    protected function getForeignKey()
    {
        return $this->snake($this->getClassServiceName($this)) . '_' . $this->getPrimaryKey();
    }

    protected function getJunctionTable(self $related)
    {
        $tables = [$this->getTable(), $related->getTable()];
        sort($tables);

        return implode('_', $tables);
    }

    protected function getRelatedValue($field)
    {
        return $this->relatedValue ?: (array_key_exists($field, $this->data) ? $this->get($field) : null);
    }

    protected function setChanged($name)
    {
        $this->changedData[$name] = isset($this->data[$name]) ? $this->data[$name] : null;
        $this->isChanged = true;
    }

    protected function resetChanged($name)
    {
        $name = $this->convertInputIdentifier($name);

        if (array_key_exists($name, $this->changedData)) {
            unset($this->changedData[$name]);
        }
        if (!$this->changedData) {
            $this->isChanged = false;
        }
        return $this;
    }

    protected function &getRelationValue($name)
    {
        /** @var static $related */
        $related = $this->{$name}();
        $serviceName = $this->getClassServiceName($related);
        $relation = $this->relations[$serviceName];
        $localValue = $this[$relation['localKey']];

        if ($related->isColl()) {
            if ($localValue) {
                $this->{$name} = $related->all();
            } else {
                $this->{$name} = $related;
            }
        } else {
            if ($localValue) {
                $this->{$name} = $related->first() ?: null;
            } else {
                $this->{$name} = null;
            }
        }

        return $this->{$name};
    }

    /**
     * Check if collection key
     *
     * @param string $key
     * @return bool
     */
    protected function isCollKey($key)
    {
        return null === $key || is_numeric($key);
    }

    /**
     * @param string $column
     * @return $this
     * @svc
     */
    protected function indexBy($column)
    {
        parent::indexBy($column);
        $this->loaded && $this->data = $this->executeIndexBy($this->data, $column);
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setColumnValue($name, $value)
    {
        // Ignore $coll[] = $value
        if (null !== $name) {
            $name = $this->convertInputIdentifier($name);

            // 如果有mutator，由mutator管理数据
            $result = $this->callSetter($name, $value);
            if ($result) {
                $this->setDataSource($name, 'db');
                // TODO 整理逻辑
                $this->changedData[$name] = isset($this->data[$name]) ? $this->data[$name] : null;
                $this->isChanged = true;
                return $this;
            }

            // 直接设置就行
            $this->origSet($name, $value);

            $this->setDataSource($name, 'user');
        } else {
            $this->origSet($name, $value);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function &getColumnValue($name)
    {
        $name = $this->convertInputIdentifier($name);
        $result = $this->callGetter($name, $value);
        if ($result) {
            return $value;
        }

        $value = $this->origGet($name);

        $source = $this->getDataSource($name);
        if ('php' === $source) {
            if ($this->isNew()) {
                // TODO 整理
                // 如果是新数据，进行转换
                $this->data[$name] = $this->getGetValue($name, $value);
            }
            // TODO 不返回 value,返回 $this->data 才有ref
            return $this->data[$name];
        }

        // 用户数据则先转换为db数据
        if ('user' === $source) {
            $value = $this->getSetValue($name, $value);
        }

        // 通过getter处理数据
        $this->data[$name] = $this->getGetValue($name, $value);
        $this->setDataSource($name, 'php');

        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param string $source
     */
    protected function setDataSource($name, $source)
    {
        $this->dataSources[$name] = $source;
    }

    /**
     * Returns the data source of specified column name
     *
     * @param string $name
     * @return string
     */
    protected function getDataSource($name)
    {
        return isset($this->dataSources[$name]) ? $this->dataSources[$name] : $this->dataSources['*'];
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    protected function getGetValue($name, $value)
    {
        $result = $this->callGetter($name, $value);
        if ($result) {
            return $value;
        } else {
            return $this->trigger('getValue', [$value, $name]);
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    protected function getSetValue($name, $value)
    {
        $result = $this->callSetter($name, $value);
        if ($result) {
            $value = $this->data[$name];
        } else {
            $value = $this->trigger('setValue', [$value, $name]);
        }

        return $value;
    }

    /**
     * Generates data for saving to database
     *
     * @return array
     */
    protected function generateDbData()
    {
        $dbData = [];
        foreach ($this->data as $name => $value) {
            if ('db' !== $this->getDataSource($name)) {
                $dbData[$name] = $this->getSetValue($name, $value);
            } else {
                $dbData[$name] = $value;
            }
        }

        return $dbData;
    }

    /**
     * Returns the service object
     *
     * @param string $name
     * @return BaseService
     * @throws \Exception
     */
    protected function &getServiceValue($name)
    {
        parent::__get($name);

        return $this->{$name};
    }

    /**
     * Returns the virtual column value
     *
     * @param string $name
     * @return mixed
     */
    protected function &getVirtualValue($name)
    {
        $result = $this->callGetter($name, $this->virtualData[$name]);
        if ($result) {
            return $this->virtualData[$name];
        }

        throw new InvalidArgumentException('Invalid virtual column: ' . $name);
    }

    /**
     * Sets the virtual column value
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setVirtualValue($name, $value)
    {
        $result = $this->callSetter($name, $value);
        if (!$result) {
            throw new InvalidArgumentException('Invalid virtual column: ' . $name);
        }

        return $this;
    }

    /**
     * Check if the name is virtual column
     *
     * @param string $name
     * @return bool
     */
    protected function hasVirtual($name)
    {
        $name = $this->convertInputIdentifier($name);

        return in_array($name, $this->virtual, true);
    }

    /**
     * Sets relation value
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setRelationValue($name, $value)
    {
        $this->relationValues[$name] = $value;
        $this->{$name} = $value;

        return $this;
    }

    /**
     * Check if model has specified relation
     *
     * @param string $name
     * @return bool
     */
    protected function hasRelation($name)
    {
        // Ignore methods in Model::class
        // TODO tags 方法容易冲突，改为其他名称 !method_exists(self::class, $name) &&
        // testSetInvalidPropertyName
        return method_exists($this, $name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    protected function callGetter($name, &$value)
    {
        $method = 'get' . $this->camel($name) . 'Attribute';
        if ($result = method_exists($this, $method)) {
            $value = $this->{$method}();
        }

        return $result;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    protected function callSetter($name, $value)
    {
        $method = 'set' . $this->camel($name) . 'Attribute';
        if ($result = method_exists($this, $method)) {
            $this->{$method}($value);
        }

        return $result;
    }

    private function baseName()
    {
        $parts = explode('\\', static::class);
        return end($parts);
    }

    private function pluralize($word)
    {
        return wei()->str->pluralize($word);
    }

    /**
     * 获取当前类的服务名称对应的类
     *
     * @return string
     */
    private static function getServiceClass()
    {
        $wei = wei();
        return $wei->has($wei->getServiceName(static::class)) ?: static::class;
    }
}
