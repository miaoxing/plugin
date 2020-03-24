<?php

namespace Miaoxing\Plugin\Service;

class Model extends QueryBuilder implements \ArrayAccess, \IteratorAggregate, \Countable
{
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
    protected $guarded = array('id');

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
    }

    /**
     * Returns the record data as array
     *
     * @param array $returnFields A indexed array specified the fields to return
     * @return array
     */
    public function toArray($returnFields = array())
    {
        if (!$this->isColl) {
            $data = array_fill_keys($returnFields ?: $this->getFields(), null);
            if (!$returnFields) {
                return $this->data + $data;
            } else {
                $data = array_fill_keys($returnFields, null);
                return array_intersect_key($this->data, $data) + $data;
            }
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
    public function isFillable($field)
    {
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
        // Check if field exists when it is not a collection
        if (!$this->isColl && !in_array($name, $this->getFields())) {
            throw new \InvalidArgumentException(sprintf(
                'Field "%s" not found in record class "%s"',
                $name,
                get_class($this)
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
     * @throws \InvalidArgumentException
     */
    public function set($name, $value = null)
    {
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
     * Check if the record's data or specified field is changed
     *
     * @param string $field
     * @return bool
     */
    public function isChanged($field = null)
    {
        if ($field) {
            return array_key_exists($field, $this->changedData);
        }
        return $this->isChanged;
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
}
