<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Services\Model\CamelCaseTrait;
use Miaoxing\Services\Model\CastTrait;
use Miaoxing\Services\Model\DefaultScopeTrait;
use Miaoxing\Services\Model\GetSetTrait;
use Miaoxing\Services\Model\ReqQueryTrait;
use Wei\Record;
use Wei\RetTrait;

class Model extends QueryBuilder implements \ArrayAccess, \IteratorAggregate, \Countable
{
    use CamelCaseTrait;
    use CastTrait;
    use ReqQueryTrait;
    use GetSetTrait;
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
     * @var array|$this[]
     */
    protected $data = array();

    /**
     * The fields that are assignable through fromArray method
     *
     * @var array
     */
    protected $fillable = array();

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
    protected $changedData = array();

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
    protected $isColl;

    /**
     * The default cache time
     *
     * @var int
     */
    protected $defaultCacheTime = 60;

    /**
     * The specified cache time
     *
     * @var int|false
     */
    protected $cacheTime = false;

    /**
     * @var string
     */
    protected $cacheKey = '';

    /**
     * The cache tags
     *
     * @var array
     */
    protected $cacheTags = array();

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
    protected $relatedValue;

    /**
     * @var array
     */
    protected $virtual = [];

    /**
     * @var array
     */
    protected $hidden = [];

    protected static $snakeCache = [];

    protected static $camelCache = [];

    protected static $booted = [];

    protected static $events = [];

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
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        // Clear changed status after created
        $this->changedData = array();
        $this->isChanged = false;

        $this->triggerCallback('afterLoad');

        $this->boot();
    }

    /**
     * Returns the record data as array
     *
     * @param array $returnFields A indexed array specified the fields to return
     * @return array
     */
    public function toArray($returnFields = array())
    {
        if (!$this->isLoaded()) {
            $this->loadData($this->isColl() ? 0 : 'id');
        }

        if (!$this->isColl) {
            $data = [];
            $columns = $this->getToArrayColumns($returnFields ?: $this->getFields());
            foreach ($columns as $column) {
                $data[$this->filterOutputColumn($column)] = $this->get($column);
            }

            return $data + $this->virtualToArray();
        } else {
            $data = array();
            /** @var $record Record */
            foreach ($this->data as $key => $record) {
                $data[$key] = $record->toArray($returnFields);
            }
            return $data;
        }
    }

    /**
     * Returns the record and relative records data as JSON string
     *
     * @param array $returnFields A indexed array specified the fields to return
     * @return array
     */
    public function toJson($returnFields = array())
    {
        return json_encode($this->toArray($returnFields));
    }

    /**
     * Import a PHP array in this record
     *
     * @param array|\ArrayAccess $data
     * @return $this
     */
    public function fromArray($data)
    {
        foreach ($data as $key => $value) {
            if (is_int($key) || $this->isFillable($key)) {
                $this->set($key, $value);
            }
        }
        return $this;
    }

    /**
     * Check if the field is assignable through fromArray method
     *
     * @param string $field
     * @return bool
     */
    public function isFillable($field, $data = null)
    {
        if ($this->trigger('checkInputColumn', [$field, $data]) === false) {
            return false;
        }

        $field = $this->filterInputColumn($field);

        return !in_array($field, $this->guarded) && !$this->fillable || in_array($field, $this->fillable);
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
     * Save the record or data to database
     *
     * @param array $data
     * @return $this
     */
    public function save($data = array())
    {
        // 1. Merges data from parameters
        $data && $this->fromArray($data);

        // 2.1 Saves single record
        if (!$this->isColl) {

            // 2.1.1 Returns when record has been destroy to avoid store dirty data
            if ($this->isDestroyed) {
                return $this;
            }

            // Deletes the record when it's waiting to remove from database
            if ($this->detached) {
                $this->db->delete($this->table, array($this->primaryKey => $this->data[$this->primaryKey]));
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

                $this->db->insert($this->table, $this->data);
                $this->isNew = false;

                // Receives primary key value when it's empty
                if (!isset($this->data[$this->primaryKey]) || !$this->data[$this->primaryKey]) {
                    // Prepare sequence name for PostgreSQL
                    $sequence = sprintf('%s_%s_seq', $this->fullTable, $this->primaryKey);
                    $this->data[$this->primaryKey] = $this->db->lastInsertId($sequence);
                }
                // 2.1.3.2 Updates existing record
            } else {
                if ($this->isChanged) {
                    $data = array_intersect_key($this->data, $this->changedData);
                    $this->db->update($this->table, $data, array(
                        $this->primaryKey => $this->data[$this->primaryKey],
                    ));
                }
            }

            // 2.1.4 Reset changed data and changed status
            $this->changedData = array();
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

        return $this;
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param mixed $conditions
     * @return $this
     */
    public function destroy($conditions = false)
    {
        $this->andWhere($conditions);
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
        $this->db->delete($this->table, array($this->primaryKey => $this->data[$this->primaryKey]));
    }

    /**
     * Reload the record data from database
     *
     * @return $this
     */
    public function reload()
    {
        $this->data = (array) $this->db->select($this->table,
            array($this->primaryKey => $this->get($this->primaryKey)));
        $this->changedData = array();
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
    public function saveColl($data, $extraData = array(), $sort = false)
    {
        if (!is_array($data)) {
            return $this;
        }

        // 1. Uses primary key as data index
        $newData = array();
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
        $existIds = array();
        foreach ($data as $row) {
            if (isset($row[$this->primaryKey]) && $row[$this->primaryKey] !== null) {
                $existIds[] = $row[$this->primaryKey];
            }
        }
        /** @var $record Record */
        foreach ($this->data as $key => $record) {
            if (!in_array($record[$this->primaryKey], $existIds)) {
                $record->destroy();
                unset($this->data[$key]);
            }
        }

        // 4. Merges existing rows or create new rows
        foreach ($data as $index => $row) {
            if ($sort) {
                $row[$sort] = $index;
            }
            if (isset($row[$this->primaryKey]) && isset($this->data[$row[$this->primaryKey]])) {
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
     * @return mixed|$this
     * @throws \InvalidArgumentException When field not found
     */
    public function get($name)
    {
        $name = $this->filterInputColumn($name);

        $method = 'get' . $this->camel($name) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        // Check if field exists when it is not a collection
        if (!$this->isColl && !in_array($name, $this->getFields())) {
            throw new \InvalidArgumentException(sprintf(
                'Field "%s" not found in record class "%s"',
                $name,
                get_class($this)
            ));
        }
        $value = isset($this->data[$name]) ? $this->data[$name] : null;

        return $this->trigger('getValue', [$value, $name]);
    }

    /**
     * Set the record field value
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throws \InvalidArgumentException
     */
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

        $this->loaded = true;

        // Set record for collection
        if (!$this->data && $value instanceof static) {
            $this->isColl = true;
        }

        if (!$this->isColl) {
            if (in_array($name, $this->getFields())) {
                $this->changedData[$name] = isset($this->data[$name]) ? $this->data[$name] : null;
                $this->data[$name] = $value;
                $this->isChanged = true;
            }
        } else {
            if (!$value instanceof static) {
                throw new \InvalidArgumentException('Value for collection must be an instance of Wei\Record');
            } else {
                // Support $coll[] = $value;
                if ($name === null) {
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
        $data = array();
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
        $name = $this->filterInputColumn($name);
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
        $name = $this->filterInputColumn($name);
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
     * @return string|array
     */
    public function getChangedData($field = null)
    {
        if ($field) {
            return isset($this->changedData[$field]) ? $this->changedData[$field] : null;
        }
        return $this->changedData;
    }

    /**
     * @return mixed
     */
    protected function fetchFromCache()
    {
        $cache = $this->cacheTags === false ? $this->cache : $this->tagCache($this->cacheTags ?: $this->getCacheTags());
        $that = $this;
        $params = $this->params;
        $paramTypes = $this->paramTypes;
        return $cache->get($this->getCacheKey(), $this->cacheTime, function () use ($that, $params, $paramTypes) {
            return $that->db->fetchAll($that->getSql(), $params, $paramTypes);
        });
    }

    /**
     * Clear cache that tagged with current table name
     *
     * @return $this
     */
    public function clearTagCache()
    {
        $this->tagCache($this->getCacheTags())->clear();
        return $this;
    }

    /**
     * Executes the generated SQL and returns the found record object or false
     *
     * @param mixed $conditions
     * @return $this|false
     */
    public function find($conditions = false)
    {
        $this->isColl = false;
        $data = $this->fetch($conditions);
        if ($data) {
            $this->data = $data + $this->data;
            $this->triggerCallback('afterFind');
            return $this;
        } else {
            return false;
        }
    }

    /**
     * Find a record by specified conditions and init with the specified data if record not found
     *
     * @param mixed $conditions
     * @param array $data
     * @return $this
     */
    public function findOrInit($conditions = false, $data = array())
    {
        if (!$this->find($conditions)) {
            // Reset status when record not found
            $this->isNew = true;

            !is_array($conditions) && $conditions = array($this->primaryKey => $conditions);

            // Convert to object to array
            if (is_object($data) && method_exists($data, 'toArray')) {
                $data = $data->toArray();
            }

            // conditions data are fillable
            $this->fromArray($data);
            $this->setData($conditions);
        }
        return $this;
    }

    /**
     * Find a record by specified conditions and throws 404 exception if record not found
     *
     * @param mixed $conditions
     * @return $this
     * @throws \Exception
     */
    public function findOne($conditions = false)
    {
        if ($this->find($conditions)) {
            return $this;
        } else {
            throw new \Exception('Record not found', 404);
        }
    }

    /**
     * Executes the generated SQL and returns the found record collection object or false
     *
     * @param mixed $conditions
     * @return $this|$this[]
     */
    public function findAll($conditions = false)
    {
        $this->isColl = true;
        $data = $this->fetchAll($conditions);

        $records = array();
        foreach ($data as $key => $row) {
            /** @var $records Record[] */
            $records[$key] = $this->db->init($this->table, $row, false);
            $records[$key]->triggerCallback('afterFind');
        }

        $this->data = $records;
        return $this;
    }

    /**
     * Find a record by primary key value
     *
     * @param mixed $value
     * @return $this|false
     */
    public function findById($value)
    {
        return $this->find(array($this->primaryKey => $value));
    }

    /**
     * Find a record by primary key value and throws 404 exception if record not found
     *
     * @param mixed $value
     * @return $this
     */
    public function findOneById($value)
    {
        return $this->findOne(array($this->primaryKey => $value));
    }

    /**
     * Find a record by primary key value and init with the specified data if record not found
     *
     * @param mixed $value
     * @param array $data
     * @return $this
     */
    public function findOrInitById($value, $data = array())
    {
        return $this->findOrInit(array($this->primaryKey => $value), $data);
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
    public function offsetGet($offset)
    {
        $this->loadData($offset);
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
     * Load record by array offset
     *
     * @param int|string $offset
     */
    protected function loadData($offset)
    {
        if (!$this->loaded && !$this->isNew) {
            if (is_numeric($offset) || is_null($offset)) {
                $this->findAll();
            } else {
                $this->find();
            }
        }
    }

    /**
     * Filters elements of the collection using a callback function
     *
     * @param Closure $fn
     * @return $this
     */
    public function filter(Closure $fn)
    {
        $data = array_filter($this->data, $fn);
        $records = $this->db->init($this->table, array(), $this->isNew);
        $records->data = $data;
        $records->isColl = true;
        $records->loaded = $this->loaded;
        return $records;
    }

    /**
     * Set or remove cache time for the query
     *
     * @param int|null|false $seconds
     * @return $this
     */
    public function cache($seconds = null)
    {
        if ($seconds === null) {
            $this->cacheTime = $this->defaultCacheTime;
        } elseif ($seconds === false) {
            $this->cacheTime = false;
        } else {
            $this->cacheTime = (int) $seconds;
        }
        return $this;
    }

    /**
     * Set or remove cache tags
     *
     * @param array|null|false $tags
     * @return $this
     */
    public function tags($tags = null)
    {
        $this->cacheTags = $tags === false ? false : $tags;
        return $this;
    }

    /**
     * Set cache key
     *
     * @param string $cacheKey
     * @return $this
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
        return $this;
    }

    /**
     * Generate cache key form query and params
     *
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey ?: md5($this->db->getDbname() . $this->getSql() . serialize($this->params) . serialize($this->paramTypes));
    }

    /**
     * @return array
     */
    protected function getCacheTags()
    {
        $tags[] = $this->getFrom();
        foreach ($this->sqlParts['join'] as $join) {
            $tags[] = $join['table'];
        }
        return $tags;
    }

    /**
     * Trigger a callback
     *
     * @param string $name
     */
    protected function triggerCallback($name)
    {
        $this->trigger($name);
        $this->$name();
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

        if (in_array($this->updatedAtColumn, $fields)) {
            $this[$this->updatedAtColumn] = date('Y-m-d H:i:s');
        }

        if (in_array($this->updatedByColumn, $fields)) {
            $this[$this->updatedByColumn] = (int) wei()->curUser['id'];
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

        if (in_array($this->createdAtColumn, $fields) && !$this[$this->createdAtColumn]) {
            $this[$this->createdAtColumn] = date('Y-m-d H:i:s');
        }

        if (in_array($this->createdByColumn, $fields) && !$this[$this->createdByColumn]) {
            $this[$this->createdByColumn] = (int) wei()->curUser['id'];
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
     * @return BaseModel|BaseModel[]
     */
    public function __invoke(string $table = null)
    {
        if (!$this->table) {
            $this->detectTable();
        }
        $this->db->addRecordClass($this->table, get_class($this));

        return $this->db($this->table);
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

            $endWiths = substr($basename, -5) === 'Model';
            if ($endWiths) {
                $endWiths && $basename = substr($basename, 0, -5);
                $this->table = $this->str->pluralize($this->snake($basename));
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

        $related->andWhere([$foreignKey => $this->getRelatedValue($localKey)]);

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
            ->where([$junctionTable . '.' . $foreignKey => $this->getRelatedValue($primaryKey)])
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
                $this->relatedValue = $ids;
                $related = $this->$name();
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

    protected function loadHasMany(self $related = null, $relation, $name)
    {
        $serviceName = $this->getClassServiceName($related);
        $records = $related ? $related->fetchAll() : [];
        foreach ($this->data as $row) {
            $rowRelation = $row->$name = $this->wei->$serviceName()->beColl();
            foreach ($records as $record) {
                if ($record[$relation['foreignKey']] == $row[$relation['localKey']]) {
                    // Remove external data
                    if (!$related->hasColumn($relation['foreignKey'])) {
                        unset($record[$relation['foreignKey']]);
                    }
                    $rowRelation[] = $this->wei->$serviceName()->setData($record);
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
        if (in_array($name, $this->requiredServices)) {
            return parent::__get($name);
        }

        // Receive relation
        if (method_exists($this, $name)) {
            return $this->getRelationValue($name);
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
     * Check if column name exists
     *
     * @param string $name
     * @return bool
     */
    public function hasColumn($name)
    {
        $name = $this->filterInputColumn($name);

        return in_array($name, $this->getFields());
    }

    /**
     * @param string $column
     * @return array|mixed|null
     */
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

    protected function setChanged($name)
    {
        $this->changedData[$name] = isset($this->data[$name]) ? $this->data[$name] : null;
        $this->isChanged = true;
    }

    protected function resetChanged($name)
    {
        $name = $this->filterInputColumn($name);

        if (array_key_exists($name, $this->changedData)) {
            unset($this->changedData[$name]);
        }
        if (!$this->changedData) {
            $this->isChanged = false;
        }
        return $this;
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
            $field = $this->filterInputColumn($field);
            return array_key_exists($field, $this->changedData);
        }
        return $this->isChanged;
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

    protected function &getRelationValue($name)
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
     * Check if collection key
     *
     * @param string $key
     * @return bool
     */
    protected function isCollKey($key)
    {
        return $key === null || is_numeric($key);
    }

    /**
     * 搜索字段是否包含某个值
     *
     * @param string $column
     * @param string $value
     * @return $this
     */
    public function whereContains($column, $value)
    {
        return $this->andWhere($column . ' LIKE ?', '%' . $value . '%');
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
        $this->andWhere($column . ' ' . $op . ' \'' . $default . '\'');

        return $this;
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

    /**
     * 根据条件查找记录,如果是新记录则保存
     *
     * @param mixed $conditions
     * @param array $data
     * @return $this
     */
    public function findOrCreate($conditions, $data = array())
    {
        $this->findOrInit($conditions, $data);
        if ($this->isNew) {
            $this->save();
        }
        return $this;
    }

    public function selectMain($column = '*')
    {
        return $this->select($this->getTable() . '.' . $column);
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
     * @param bool $bool
     * @param mixed $conditions
     * @param array $params
     * @param array $types
     * @return $this
     */
    public function whenWhere($bool, $conditions, $params = array(), $types = array())
    {
        if ($bool) {
            return $this->andWhere($conditions, $params, $types);
        }
        return $this;
    }
}
