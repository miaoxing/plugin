<?php

namespace Miaoxing\Plugin\Model;

use Closure;
use InvalidArgumentException;
use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Db\BaseDriver;
use Miaoxing\Plugin\Service\WeiBaseModel;
use Wei\RetTrait;

/**
 * @internal
 */
trait ModelTrait
{
    use QueryBuilderTrait {
        add as private parentAdd;
        execute as private parentExecute;
        indexBy as private parentIndexBy;
    }
    use QueryBuilderCacheTrait;
    use CastTrait;
    use RetTrait;
    use DefaultScopeTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        if (isset($options['isNew']) && false === $options['isNew']) {
            $this->setDbAttributes($options['attributes']);
            unset($options['attributes']);
        }

        parent::__construct($options);

        // Clear changed status after created
        $this->changes = [];

        $this->triggerCallback('afterLoad');

        $this->boot();

        $this->trigger('init');
    }

    /**
     * @return $this|$this[]
     */
    public function __invoke(string $table = null)
    {
        $this->db->addRecordClass($this->getTable(), static::class);

        // @phpstan-ignore-next-line 待 db 服务更新后再移除
        return $this->db($this->getTable());
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
     * Check if property exists
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->$name);
    }

    /**
     * Create a new model object
     *
     * @param array $attributes
     * @param array $options
     * @return $this
     */
    public static function new($attributes = [], array $options = [])
    {
        $class = static::getServiceClass();
        return new $class($options + ['attributes' => $attributes]);
    }

    /**
     * @param array $attributes
     * @return $this|$this[]
     * @todo 待 data 改为 default ？ 后移除
     */
    public static function newColl($attributes = [])
    {
        $class = static::getServiceClass();
        return (new $class())->beColl()->fromArray($attributes);
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
     * Get guarded columns
     *
     * @return string[]
     */
    public function getGuarded()
    {
        return $this->guarded;
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
     * Get fillable columns
     *
     * @return string[]
     */
    public function getFillable()
    {
        return $this->fillable;
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
        $fillable = $this->getFillable();
        return !in_array($field, $this->getGuarded(), true) && !$fillable || in_array($field, $fillable, true);
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $attributes
     * @return $this
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $column => $value) {
            $this->set($column, $value);
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

        $primaryKey = $this->getPrimaryKey();
        $this->attributes = $this->executeSelect([$primaryKey => $this->get($primaryKey)]);
        $this->changes = [];
        $this->triggerCallback('afterLoad');
        return $this;
    }

    /**
     * Merges attributes into collection and save to database, including insert, update and delete
     *
     * @param array $attributes A two-dimensional array
     * @param array $extra The extra attributes for new rows
     * @param bool $sort
     * @return $this
     */
    public function saveColl($attributes, $extra = [], $sort = false)
    {
        if (!is_array($attributes)) {
            return $this;
        }

        // 1. Uses primary key as data index
        $newAttributes = [];
        $primaryKey = $this->getPrimaryKey();
        foreach ($this->attributes as $key => $record) {
            unset($this->attributes[$key]);
            // Ignore default data
            if ($record instanceof $this) {
                $newAttributes[$record[$primaryKey]] = $record;
            }
        }
        $this->attributes = $newAttributes;

        // 2. Removes empty rows from data
        foreach ($attributes as $index => $row) {
            if (!array_filter($row)) {
                unset($attributes[$index]);
            }
        }

        // 3. Removes missing rows
        $existIds = [];
        foreach ($attributes as $row) {
            if (isset($row[$primaryKey]) && null !== $row[$primaryKey]) {
                $existIds[] = $row[$primaryKey];
            }
        }
        /** @var static $record */
        foreach ($this->attributes as $key => $record) {
            if (!in_array($record[$primaryKey], $existIds, true)) {
                $record->destroy();
                unset($this->attributes[$key]);
            }
        }

        // 4. Merges existing rows or create new rows
        foreach ($attributes as $index => $row) {
            if ($sort) {
                $row[$sort] = $index;
            }
            if (isset($row[$primaryKey], $this->attributes[$row[$primaryKey]])) {
                $this->attributes[$row[$primaryKey]]->fromArray($row);
            } else {
                $this->offsetSet(null, $this->__invoke($this->table)->fromArray($extra + $row));
            }
        }
        $this->attributes = array_values($this->attributes);

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
        if ($this->isCollKey($name) || $this->hasColumn($name) || array_key_exists($name, $this->attributes)) {
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
        if (!$this->isColl && !in_array($name, $this->getColumns(), true)) {
            throw new InvalidArgumentException(sprintf(
                'Field "%s" not found in record class "%s"',
                $name,
                static::class
            ));
        }
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Set the record field value
     *
     * @param string|int|null $name
     * @param mixed $value
     * @return $this
     * @throws InvalidArgumentException
     */
    public function origSet($name, $value = null)
    {
        $this->loaded = true;

        // Set record for collection
        if (!$this->attributes && $value instanceof static) {
            $this->isColl = true;
        }

        if (!$this->isColl) {
            if (in_array($name, $this->getColumns(), true)) {
                $this->changes[$name] = isset($this->attributes[$name]) ? $this->attributes[$name] : null;
                $this->attributes[$name] = $value;
            }
        } else {
            if (!$value instanceof static) {
                throw new InvalidArgumentException('Value for collection must be an instance of Wei\Record');
            } else {
                // Support $coll[] = $value;
                if (null === $name) {
                    $this->attributes[] = $value;
                } else {
                    $this->attributes[$name] = $value;
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
        foreach ($this->attributes as $record) {
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
        $attributes = [];
        foreach ($this->attributes as $record) {
            $attributes[] = $record[$name];
        }
        return $attributes;
    }

    /**
     * Remove field value
     *
     * @param string $name The name of field
     * @return $this
     */
    public function remove($name)
    {
        if (!$this->isColl) {
            if (array_key_exists($name, $this->attributes)) {
                $this->attributes[$name] = null;
            }
        } else {
            unset($this->attributes[$name]);
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
        $this[$name] = (object) ($this->convertToDbKey($name) . ' + ' . $offset);
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
        $this[$name] = (object) ($this->convertToDbKey($name) . ' - ' . $offset);
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
     * Return the column that has been changed
     *
     * @param string $column
     * @return array|string|null
     */
    public function getChanges($column = null)
    {
        if ($column) {
            return isset($this->changes[$column]) ? $this->changes[$column] : null;
        }
        return $this->changes;
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
        $this->loadAttributes($offset);
        return isset($this->attributes[$offset]);
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
     * @param string|int|null $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->loadAttributes($offset);
        $this->set($offset, $value);
    }

    /**
     * Unset the offset
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->loadAttributes($offset);
        $this->remove($offset);
    }

    /**
     * Retrieve an array iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        $this->loadAttributes(0);
        return new \ArrayIterator($this->attributes);
    }

    /**
     * Filters elements of the collection using a callback function
     *
     * @param Closure $fn
     * @return $this|$this[]
     */
    public function filter(Closure $fn)
    {
        $attributes = array_filter($this->attributes, $fn);
        return static::newColl($attributes)->setOption([
            'isNew' => $this->isNew,
            'loaded' => $this->loaded,
        ]);
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
        $fields = $this->getColumns();

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
        $fields = $this->getColumns();

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
     * @param object $class
     * @param bool $autoload
     * @return array
     * @see https://www.php.net/manual/en/function.class-uses.php#112671
     */
    public function classUsesDeep($class, $autoload = true)
    {
        $traits = [];

        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));

        // Get traits of all parent traits
        $traitsToSearch = $traits;
        while (!empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        };

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
        $this->attributes = [];
        $this->isColl = true;

        return $this;
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
        return $this->setCacheTags($this->getUserTag());
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
     * Returns the data of model
     *
     * @return $this[]|array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get hidden columns
     *
     * @return string[]
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set hidden columns
     *
     * @param string|array $hidden
     * @return $this
     */
    public function setHidden($hidden)
    {
        $this->hidden = (array) $hidden;

        return $this;
    }

    /**
     * @param self|string $record
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return static
     */
    public function hasOne($record, $foreignKey = null, $localKey = null)
    {
        $related = $this->getRelatedModel($record);
        $name = $related->getClassServiceName();

        $localKey || $localKey = $this->getPrimaryKey();
        $foreignKey || $foreignKey = $this->getForeignKey();
        $this->addRelation($name, ['foreignKey' => $foreignKey, 'localKey' => $localKey]);

        $value = $this->getRelatedValue($localKey);
        $related->where($foreignKey, $value);
        $related->setRelationAttribute($foreignKey, $value);

        return $related;
    }

    /**
     * @param self|string $record
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return $this
     */
    public function hasMany($record, $foreignKey = null, $localKey = null)
    {
        return $this->hasOne($record, $foreignKey, $localKey)->beColl();
    }

    /**
     * @param self|string $record
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return static
     */
    public function belongsTo($record, $foreignKey = null, $localKey = null)
    {
        $related = $this->getRelatedModel($record);
        $foreignKey || $foreignKey = $this->getPrimaryKey();
        $localKey || $localKey = $this->snake($related->getClassServiceName()) . '_' . $this->getPrimaryKey();

        return $this->hasOne($related, $foreignKey, $localKey);
    }

    /**
     * @param self|string $record
     * @param string|null $junctionTable
     * @param string|null $foreignKey
     * @param string|null $relatedKey
     * @return static
     */
    public function belongsToMany($record, $junctionTable = null, $foreignKey = null, $relatedKey = null)
    {
        $related = $this->getRelatedModel($record);
        $name = $this->getClassServiceName($related);

        $primaryKey = $this->getPrimaryKey();
        $junctionTable || $junctionTable = $this->getJunctionTable($related);
        $foreignKey || $foreignKey = $this->getForeignKey();
        $relatedKey || $relatedKey = $this->snake($name) . '_' . $primaryKey;
        $this->addRelation($name, [
            'junctionTable' => $junctionTable,
            'relatedKey' => $relatedKey,
            'foreignKey' => $foreignKey,
            'localKey' => $primaryKey,
        ]);

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
     *
     * @return $this|BaseService
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
        return in_array($name, $this->getColumns(), true);
    }

    public function trigger($event, $data = [])
    {
        $result = null;
        $class = static::class;
        if (isset(static::$modelEvents[$class][$event])) {
            foreach (static::$modelEvents[$class][$event] as $callback) {
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
        static::$modelEvents[static::class][$event][] = $method;
    }

    public function execute()
    {
        $this->trigger('preExecute');

        if (BaseDriver::SELECT == $this->queryType) {
            $this->loaded = true;
        }

        return $this->parentExecute();
    }

    public function add($sqlPartName, $sqlPart, $append = false, $type = null)
    {
        $this->trigger('preBuildQuery', func_get_args());

        $this->isNew = false;

        return $this->parentAdd($sqlPartName, $sqlPart, $append, $type);
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
            return array_key_exists($field, $this->changes);
        }
        return (bool) $this->changes;
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return $this
     */
    public function setRawValue($column, $value)
    {
        $this->attributes[$column] = $value;
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
        $this->loadAttributes(0);
        return count($this->attributes);
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
    public function setDbAttributes(array $data)
    {
        $this->attributes = $data + $this->attributes;

        if ($data) {
            $this->loaded = true;
            $this->setDataSource('*', 'db');
        }

        return $this;
    }

    /**
     * Save with relation data
     *
     * Use with relation calls like
     *
     * ```php
     * $user->profile()->saveRelation([]);
     * $user->emails()->saveRelation([[], []]);
     * ```
     *
     * @param array $attributes
     * @return $this
     * @expertimental
     */
    public function saveRelation(array $attributes = [])
    {
        if ($this->isColl()) {
            $this->all();
            $this->saveColl($attributes, $this->relationAttributes);
        } else {
            $this->findOrInitBy([])->fromArray($attributes)->save($this->relationAttributes);
        }

        return $this;
    }

    /**
     * Returns whether the model was inserted in the this request
     *
     * @return bool
     */
    public function wasRecentlyCreated()
    {
        return $this->wasRecentlyCreated;
    }

    /**
     * Sets the primary key column
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
     * Returns the primary key column
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Returns the record data as array
     *
     * @param array|callable $returnFields A indexed array specified the fields to return
     * @param callable|null $prepend
     * @return array
     * @svc
     */
    protected function toArray($returnFields = [], callable $prepend = null)
    {
        if (!$this->isLoaded()) {
            $this->loadAttributes($this->isColl() ? 0 : 'id');
        }

        if (!$this->isColl) {
            $data = [];
            $columns = $this->getToArrayColumns($returnFields ?: $this->getColumns());
            foreach ($columns as $column) {
                $data[$column] = $this->get($column);
            }

            return $data + $this->virtualToArray() + $this->relationToArray();
        } else {
            if (is_callable($returnFields)) {
                $prepend = $returnFields;
                $returnFields = [];
            }

            $data = [];
            /** @var static $record */
            foreach ($this->attributes as $key => $record) {
                $data[$key] = $record->toArray($returnFields);
                if ($prepend) {
                    $data[$key] = $prepend($record) + $data[$key];
                }
            }
            return $data;
        }
    }

    /**
     * 不经过fillable检查,设置数据并保存
     *
     * @param array $attributes
     * @return $this
     * @svc
     */
    protected function saveAttributes($attributes = [])
    {
        $attributes && $this->setAttributes($attributes);

        return $this->save();
    }

    /**
     * Returns the success result with model data
     *
     * @param array $merge
     * @return Ret
     * @svc
     */
    protected function toRet(array $merge = [])
    {
        if ($this->isColl()) {
            return $this->suc($merge + [
                    'data' => $this,
                    'page' => $this->getQueryPart('page'),
                    'limit' => $this->getQueryPart('limit'),
                    'total' => $this->cnt(),
                ]);
        } else {
            return $this->suc($merge + ['data' => $this])->setMetadata('model', $this);
        }
    }

    /**
     * Return the record table name
     *
     * @return string
     * @svc
     */
    protected function getTable()
    {
        if (!isset($this->table)) {
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
     * @param array|\ArrayAccess $attributes
     * @return $this
     * @svc
     */
    protected function fromArray($attributes)
    {
        foreach ($attributes as $key => $value) {
            if (is_int($key) || $this->isFillable($key, $attributes)) {
                $this->setFromArray($key, $value);
            }
        }
        return $this;
    }

    /**
     * Save the record or data to database
     *
     * @param array $attributes
     * @return $this
     * @svc
     */
    protected function save($attributes = [])
    {
        // 1. Merges attributes from parameters
        $attributes && $this->fromArray($attributes);

        // 2.1 Saves single record
        if (!$this->isColl) {
            $primaryKey = $this->getPrimaryKey();

            // 2.1.2 Triggers before callbacks
            $isNew = $this->isNew;
            $this->triggerCallback('beforeSave');
            $this->triggerCallback($isNew ? 'beforeCreate' : 'beforeUpdate');

            // 将数据转换为数据库数据
            $origAttributes = $this->attributes;
            $this->attributes = $this->getDbAttributes();
            $isNew = $this->isNew;

            // 2.1.3.1 Inserts new record
            if ($isNew) {
                // Removes primary key value when it's empty to avoid SQL error
                if (array_key_exists($primaryKey, $this->attributes) && !$this->attributes[$primaryKey]) {
                    unset($this->attributes[$primaryKey]);
                }

                $this->executeInsert($this->attributes);
                $this->isNew = false;
                $this->wasRecentlyCreated = true;

                // Receives primary key value when it's empty
                if (!isset($this->attributes[$primaryKey]) || !$this->attributes[$primaryKey]) {
                    // Prepare sequence name for PostgreSQL
                    $sequence = sprintf('%s_%s_seq', $this->db->getTable($this->getTable()), $primaryKey);
                    $this->attributes[$primaryKey] = $this->db->lastInsertId($sequence);
                }
                // 2.1.3.2 Updates existing record
            } else {
                if ($this->isChanged()) {
                    $attributes = array_intersect_key($this->attributes, $this->changes);
                    $this->executeUpdate($attributes, [$primaryKey => $this->attributes[$primaryKey]]);
                }
            }

            if ($isNew) {
                $this->setDataSource($primaryKey, 'db');
            }

            // 解决保存之前调用了$this->id导致变为null的问题
            if ($isNew && array_key_exists($primaryKey, $origAttributes)) {
                $origAttributes[$primaryKey] = $this->attributes[$primaryKey];
            }

            // 还原原来的数据+save过程中生成的主键数据
            $this->attributes = $origAttributes + $this->attributes;

            // 2.1.4 Reset changed attributes
            $this->changes = [];

            // 2.1.5. Triggers after callbacks
            $this->triggerCallback($isNew ? 'afterCreate' : 'afterUpdate');
            $this->triggerCallback('afterSave');
            // 2.2 Loop and save collection records
        } else {
            foreach ($this->attributes as $record) {
                $record->save();
            }
        }

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
        !$this->loaded && $this->loadAttributes(0);

        if (!$this->isColl) {
            $this->triggerCallback('beforeDestroy');

            $result = $this->trigger('destroy');
            if (!$result) {
                $this->executeDestroy();
            }

            $this->triggerCallback('afterDestroy');
        } else {
            foreach ($this->attributes as $record) {
                $record->destroy();
            }
        }

        return $this;
    }

    protected function executeDestroy()
    {
        $primaryKey = $this->getPrimaryKey();
        $this->executeDelete([$primaryKey => $this->attributes[$primaryKey]]);
        $this->isNew = true;
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
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
     * @param int|string|array|null $id
     * @return $this|null
     * @svc
     */
    protected function find($id)
    {
        return $this->findBy($this->getPrimaryKey(), $id);
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
     * Find a record by primary key, or init with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array|object $attributes
     * @return $this
     * @svc
     */
    protected function findOrInit($id = null, $attributes = [])
    {
        return $this->findOrInitBy([$this->getPrimaryKey() => $id], $attributes);
    }

    /**
     * Find a record by primary key, or save with the specified attributes if record not found
     *
     * @param int|string $id
     * @param array $attributes
     * @return $this
     * @svc
     */
    protected function findOrCreate($id, $attributes = [])
    {
        $this->findOrInit($id, $attributes);
        if ($this->isNew()) {
            $this->save();
        }
        return $this;
    }

    /**
     * @param array $attributes
     * @param array $data
     * @return $this
     * @svc
     */
    protected function findByOrCreate($attributes, $data = [])
    {
        $this->findOrInitBy($attributes, $data);
        if ($this->isChanged()) {
            $this->save();
        }
        return $this;
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
        return $this->findAllBy($this->getPrimaryKey(), 'IN', $ids);
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|null
     * @svc
     */
    protected function findBy($column, $operator = null, $value = null)
    {
        $this->isColl = false;
        $data = $this->fetch(...func_get_args());
        if ($data) {
            $this->setDataSource('*', 'db');
            $this->attributes = $data + $this->attributes;
            $this->triggerCallback('afterFind');
            return $this;
        } else {
            return null;
        }
    }

    /**
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
     * @return $this|$this[]
     * @svc
     */
    protected function findAllBy($column, $operator = null, $value = null)
    {
        $this->isColl = true;
        $data = $this->fetchAll(...func_get_args());

        $records = [];
        foreach ($data as $key => $row) {
            /** @var static[] $records */
            $records[$key] = static::new($row, [
                'wei' => $this->wei,
                'db' => $this->db,
                'table' => $this->getTable(),
                'isNew' => false,
            ]);
            $records[$key]->triggerCallback('afterFind');
        }

        $this->attributes = $records;
        return $this;
    }

    /**
     * @param array $attributes
     * @param array|object $data
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

            $this->setAttributes($attributes);
            $this->fromArray($data);
        }
        return $this;
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param mixed $column
     * @param mixed|null $operator
     * @param mixed|null $value
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
     * @param array|Req|null $req
     * @return $this
     * @throws \Exception
     * @svc
     */
    protected function findFromReq($req = null)
    {
        $req || $req = $this->wei->req;
        if (!$req->isPost()) {
            $this->findOrFail($req[$this->getPrimaryKey()]);
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
     * @return $this|$this[]|array
     * @svc
     */
    protected function all()
    {
        return $this->findAllBy(null);
    }

    /**
     * Load record by array offset
     *
     * @param int|string|null $offset
     */
    protected function loadAttributes($offset)
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
        if ($hidden = $this->getHidden()) {
            $columns = array_diff($columns, $hidden);
        }

        return $columns;
    }

    protected function virtualToArray()
    {
        $data = [];
        foreach ($this->virtual as $column) {
            $data[$column] = $this->{'get' . $this->camel($column) . 'Attribute'}();
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
     * @return static
     */
    protected function getRelatedModel($model)
    {
        if ($model instanceof static) {
            return $model;
        } elseif (is_subclass_of($model, WeiBaseModel::class)) {
            return forward_static_call([$model, 'new']);
        } else {
            return $this->wei->{$model}();
        }
    }

    /**
     * @param self|null $related
     * @param array $relation
     * @param string $name
     * @return array|$this|$this[]
     */
    protected function loadHasOne($related, $relation, $name)
    {
        if ($related) {
            $records = $related->all()->indexBy($relation['foreignKey']);
        } else {
            $records = [];
        }
        foreach ($this->attributes as $row) {
            $row->{$name} = isset($records[$row[$relation['localKey']]]) ? $records[$row[$relation['localKey']]] : null;
        }

        return $records;
    }

    /**
     * @param self|null $related
     * @param array $relation
     * @param string $name
     * @return array|$this|$this[]
     */
    protected function loadHasMany($related, $relation, $name)
    {
        $records = $related ? $related->fetchAll() : [];
        foreach ($this->attributes as $row) {
            $rowRelation = $row->{$name} = $related::newColl();
            foreach ($records as $record) {
                // NOTE: 从数据库取出为 string, 因此必须转换了再比较
                if ($record[$relation['foreignKey']] === (string) $row[$relation['localKey']]) {
                    // Remove external data
                    if (!$related->hasColumn($relation['foreignKey'])) {
                        unset($record[$relation['foreignKey']]);
                    }
                    $rowRelation[] = forward_static_call([$related, 'new'])->setAttributes($record);
                }
            }
        }

        return $records;
    }

    /**
     * @param self|null $related
     * @param array $relation
     * @param string $name
     * @return array|$this|$this[]
     */
    protected function loadBelongsToMany($related, $relation, $name)
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

    protected function getJunctionTable(WeiBaseModel $related)
    {
        $tables = [$this->getTable(), $related->getTable()];
        sort($tables);

        return implode('_', $tables);
    }

    protected function getRelatedValue($field)
    {
        return $this->relatedValue ?: (array_key_exists($field, $this->attributes) ? $this->get($field) : null);
    }

    protected function setChanged($name)
    {
        $this->changes[$name] = isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    protected function resetChanged($name)
    {
        if (array_key_exists($name, $this->changes)) {
            unset($this->changes[$name]);
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
     * @param string|null $key
     * @return bool
     */
    protected function isCollKey(string $key = null)
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
        $this->parentIndexBy($column);
        $this->loaded && $this->attributes = $this->executeIndexBy($this->attributes, $column);
        return $this;
    }

    /**
     * @param string|null $name
     * @param mixed $value
     * @return $this
     */
    protected function setColumnValue($name, $value)
    {
        // Ignore $coll[] = $value
        if (null !== $name) {
            // 如果有mutator，由mutator管理数据
            $result = $this->callSetter($name, $value);
            if ($result) {
                $this->setDataSource($name, 'db');
                // TODO 整理逻辑
                $this->changes[$name] = isset($this->attributes[$name]) ? $this->attributes[$name] : null;
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
                $this->attributes[$name] = $this->getGetValue($name, $value);
            }
            // TODO 不返回 value,返回 $this->data 才有ref
            return $this->attributes[$name];
        }

        // 用户数据则先转换为db数据
        if ('user' === $source) {
            $value = $this->getSetValue($name, $value);
        }

        // 通过getter处理数据
        $this->attributes[$name] = $this->getGetValue($name, $value);
        $this->setDataSource($name, 'php');

        return $this->attributes[$name];
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
            $value = $this->attributes[$name];
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
    protected function getDbAttributes()
    {
        $dbData = [];
        foreach ($this->attributes as $name => $value) {
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
        $result = $this->callGetter($name, $this->virtualAttributes[$name]);
        if ($result) {
            return $this->virtualAttributes[$name];
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

    /**
     * @param string $column
     * @param mixed $value
     * @return $this
     * @internal
     */
    protected function setRelationAttribute(string $column, $value)
    {
        $this->relationAttributes[$this->convertToPhpKey($column)] = $value;
        return $this;
    }

    /**
     * @todo 整理
     */
    private function setFromArray($name, $value)
    {
        if ($this->isCollKey($name) || $this->hasColumn($name)) {
            return $this->setColumnValue($name, $value);
        }

        if ($this->hasVirtual($name)) {
            return $this->setVirtualValue($name, $value);
        }

        return false;
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

    /**
     * @param array $conditions
     * @return int
     * @internal
     */
    private function executeDelete(array $conditions)
    {
        return $this->db->delete($this->getTable(), $this->convertKeysToDbKeys($conditions));
    }

    /**
     * @param array $conditions
     * @return array
     * @internal
     */
    private function executeSelect(array $conditions)
    {
        return $this->convertKeysToPhpKeys(
            (array) $this->db->select($this->getTable(), $this->convertKeysToDbKeys($conditions))
        );
    }

    /**
     * @param array $data
     * @return int
     * @internal
     */
    private function executeInsert(array $data)
    {
        return $this->db->insert($this->getTable(), $this->convertKeysToDbKeys($data));
    }

    /**
     * @param array $data
     * @param array $conditions
     * @return int
     * @internal
     */
    private function executeUpdate(array $data, array $conditions)
    {
        return $this->db->update(
            $this->getTable(),
            $this->convertKeysToDbKeys($data),
            $this->convertKeysToDbKeys($conditions)
        );
    }

    /**
     * @param string $name
     * @param array $relation
     * @internal
     */
    private function addRelation($name, $relation)
    {
        $this->relations[$name] = array_map([$this, 'convertToPhpKey'], $relation);
    }
}
