<?php

namespace Miaoxing\Plugin\Model;

use InvalidArgumentException;
use Miaoxing\Plugin\BaseService;
use Wei\Base;
use Wei\Req;
use Wei\Ret;
use Wei\RetTrait;
use Wei\Wei;

/**
 * The main functions of the model, expected to be used with \Wei\BaseModel
 */
trait ModelTrait
{
    use QueryBuilderTrait {
        addQueryPart as private parentAddQueryPart;
        execute as private parentExecute;
        indexBy as private parentIndexBy;
    }
    use QueryBuilderCacheTrait;
    use CollTrait;
    use CastTrait;
    use RetTrait;
    use DefaultScopeTrait;
    use RelationTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        // 1. Init service container
        $this->wei = $options['wei'] ?? Wei::getContainer();
        $this->db = $options['db'] ?? $this->wei->db;

        // 2. Set common and model config before set options
        $this->boot();
        $this->trigger('init');

        // 3. Add default value to model attributes
        $this->attributes += $this->getColumnValues('default');
        parent::__construct($options);

        // 4. Clear changed status after set attributes
        $this->resetChanges();
    }

    /**
     * Create a new model object
     *
     * @param array $attributes
     * @param array $options
     * @return $this
     */
    public static function new($attributes = [], array $options = []): self
    {
        $class = static::getServiceClass();
        return new $class($options + ['attributes' => $attributes]);
    }

    /**
     * Returns the record and relative records data as JSON string
     *
     * @param array $returnFields A indexed array specified the fields to return
     * @return string
     */
    public function toJson(array $returnFields = []): string
    {
        return json_encode($this->toArray($returnFields));
    }

    /**
     * Get guarded columns
     *
     * @return string[]
     */
    public function getGuarded(): array
    {
        return $this->guarded;
    }

    /**
     * Set guarded columns
     *
     * @param array $guarded
     * @return $this
     */
    public function setGuarded(array $guarded): self
    {
        $this->guarded = $guarded;
        return $this;
    }

    /**
     * Get fillable columns
     *
     * @return string[]
     */
    public function getFillable(): array
    {
        return $this->fillable;
    }

    /**
     * Set fillable columns
     *
     * @param array $fillable
     * @return $this
     */
    public function setFillable(array $fillable): self
    {
        $this->fillable = $fillable;
        return $this;
    }

    /**
     * Check if the field is assignable through fromArray method
     *
     * @param string $column
     * @return bool
     */
    public function isFillable(string $column): bool
    {
        $fillable = $this->getFillable();
        return !in_array($column, $this->getGuarded(), true) && !$fillable || in_array($column, $fillable, true);
    }

    /**
     * Set each attribute value, without checking whether the column is fillable
     *
     * @param iterable $attributes
     * @return $this
     */
    public function setAttributes(iterable $attributes): self
    {
        // Replace all attributes of the collection
        if ($this->coll) {
            $this->attributes = [];
        }
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
    public function reload(): self
    {
        $primaryKey = $this->getPrimaryKey();
        $this->setDbAttributes($this->executeSelect([$primaryKey => $this->get($primaryKey)]));
        $this->resetChanges();
        return $this;
    }

    /**
     * Receives the model column value
     *
     * @param string|int $name
     * @param bool|null $exists
     * @param bool $throwException
     * @return mixed
     * @throws InvalidArgumentException When column not found
     */
    public function &get($name, bool &$exists = null, bool $throwException = true)
    {
        $exists = true;

        // Receive collection value
        if ($this->coll && $this->hasColl($name)) {
            return $this->getCollValue($name);
        }

        // Receive column value
        if ($this->hasColumn($name)) {
            return $this->getColumnValue($name);
        }

        // Receive virtual column value
        if ($this->hasVirtual($name)) {
            return $this->getVirtualValue($name);
        }

        // Receive relation
        $result = $this->getRelationValue($name, $exists, $throwException);
        if ($exists) {
            return $result;
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
     * Remove the attribute value by name
     *
     * @param string|int $name The name of field
     * @return $this
     */
    public function remove($name): self
    {
        unset($this->attributes[$name]);
        $this->setAttributeSource($name, static::ATTRIBUTE_SOURCE_USER);
        return $this;
    }

    /**
     * Increment a field
     *
     * @param string $name
     * @param int|float|string $offset
     * @return $this
     */
    public function incr(string $name, $offset = 1): self
    {
        $this[$name] = (object) ($this->convertToDbKey($name) . ' + ' . $offset);
        return $this;
    }

    /**
     * Decrement a field
     *
     * @param string $name
     * @param int|float|string $offset
     * @return $this
     */
    public function decr(string $name, $offset = 1): self
    {
        $this[$name] = (object) ($this->convertToDbKey($name) . ' - ' . $offset);
        return $this;
    }

    /**
     * Check if it's a new record and has not save to database
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->new;
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
        if ($this->hasColumn($this->updatedAtColumn)) {
            $this->setColumnValue($this->updatedAtColumn, date('Y-m-d H:i:s'));
        }

        if ($this->hasColumn($this->updatedByColumn)) {
            $this->setColumnValue($this->updatedByColumn, (int) $this->user->id);
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
        if ($this->hasColumn($this->createdAtColumn) && !$this->getColumnValue($this->createdAtColumn)) {
            $this->setColumnValue($this->createdAtColumn, date('Y-m-d H:i:s'));
        }

        if ($this->hasColumn($this->createdByColumn) && !$this->getColumnValue($this->createdByColumn)) {
            $this->setColumnValue($this->createdByColumn, (int) $this->user->id);
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

    public function boot(): void
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
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Get hidden columns
     *
     * @return string[]
     */
    public function getHidden(): array
    {
        return $this->hidden;
    }

    /**
     * Set hidden columns
     *
     * @param string|array $hidden
     * @return $this
     */
    public function setHidden($hidden): self
    {
        $this->hidden = (array) $hidden;

        return $this;
    }

    /**
     * @param string $event
     * @param array $data
     * @return mixed
     */
    public function trigger(string $event, $data = [])
    {
        $result = null;
        $class = static::class;
        if (isset(static::$modelEvents[$class][$event])) {
            foreach (static::$modelEvents[$class][$event] as $callback) {
                // 优先使用自身方法
                if (is_string($callback) && method_exists($this, $callback)) {
                    $callback = [$this, $callback];
                }
                $result = call_user_func_array($callback, (array) $data);
            }
        } else {
            $result = is_array($data) ? current($data) : $data;
        }

        return $result;
    }

    /**
     * @param string $event
     * @param string|callable $method
     */
    public static function on(string $event, $method)
    {
        static::$modelEvents[static::class][$event][] = $method;
    }

    /**
     * Check if the model's attributes or the specified column is changed
     *
     * @param string|null $column
     * @return bool
     */
    public function isChanged(string $column = null): bool
    {
        $this->removeUnchanged($column);

        if ($column) {
            return array_key_exists($column, $this->changes);
        }
        return (bool) $this->changes;
    }

    /**
     * Return the column that has been changed
     *
     * @param string|null $column
     * @return array|string|null
     */
    public function getChanges(string $column = null)
    {
        $this->removeUnchanged($column);

        if ($column) {
            return $this->changes[$column] ?? null;
        }
        return $this->changes;
    }

    /**
     * @param string $name
     * @param int|float|string $offset
     * @return $this
     */
    public function incrSave(string $name, $offset = 1): self
    {
        $value = $this->get($name) + $offset;
        $this->incr($name, $offset)->save();
        $this->set($name, $value);
        $this->resetChanges($name);

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function existOrFail(): self
    {
        if ($this->new) {
            throw new \Exception('Record not found', 404);
        }
        return $this;
    }

    /**
     * Returns whether the model was inserted in the this request
     *
     * @return bool
     */
    public function wasRecentlyCreated(): bool
    {
        return $this->wasRecentlyCreated;
    }

    /**
     * Sets the primary key column
     *
     * @param string $primaryKey
     * @return $this
     */
    public function setPrimaryKey(string $primaryKey): self
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    /**
     * Returns the primary key column
     *
     * @return string
     */
    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * Check if the offset exists
     *
     * @param string|int $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    /**
     * Get the offset value
     *
     * @param string|int $offset
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
        $this->set($offset, $value);
    }

    /**
     * Unset the offset
     *
     * @param string|int $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @param string|int $name
     * @return BaseService|$this
     * @throws \Exception
     */
    public function &__get($name)
    {
        $value = &$this->get($name, $exists, false);
        if ($exists) {
            return $value;
        }

        // Receive other services
        return $this->getServiceValue($name);
    }

    /**
     * @param string|int|null $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value = null)
    {
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
     * @param string|int $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return isset($this->$name);
    }

    /**
     * Remove the attribute value by name
     *
     * @param string|int $name The name of field
     */
    public function __unset($name): void
    {
        $this->remove($name);
    }

    /**
     * Returns the record data as array
     *
     * @param array|callable $returnFields A indexed array specified the fields to return
     * @param callable|null $prepend
     * @return array
     * @svc
     */
    protected function toArray($returnFields = [], callable $prepend = null): array
    {
        if ($this->coll) {
            return $this->mapColl(__FUNCTION__, func_get_args());
        }

        if (is_callable($returnFields)) {
            $prepend = $returnFields;
            $returnFields = [];
        }

        $data = [];
        $columns = $this->getToArrayColumns($returnFields ?: $this->getColumnNames());
        foreach ($columns as $column) {
            $data[$column] = $this->get($column);
        }

        if ($prepend) {
            $data = $prepend($this) + $data;
        }
        return $data + $this->virtualToArray() + $this->relationToArray();
    }

    /**
     * Set each attribute value, without checking whether the column is fillable, and save the model
     *
     * @param iterable $attributes
     * @return $this
     * @svc
     */
    protected function saveAttributes(iterable $attributes = []): self
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
    protected function toRet(array $merge = []): Ret
    {
        if ($this->coll) {
            return $this->collToRet($merge);
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
    protected function getTable(): string
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
     * @param iterable $array
     * @return $this
     * @svc
     */
    protected function fromArray(iterable $array): self
    {
        if ($this->coll) {
            return $this->setAttributes($array);
        }

        foreach ($array as $name => $value) {
            if (!$this->isFillable($name)) {
                continue;
            }

            if ($this->hasColumn($name)) {
                $this->setColumnValue($name, $value);
                continue;
            }

            if ($this->hasVirtual($name)) {
                $this->setVirtualValue($name, $value);
            }
        }
        return $this;
    }

    /**
     * Save the record or data to database
     *
     * @param iterable $attributes
     * @return $this
     * @svc
     */
    protected function save(iterable $attributes = []): self
    {
        // 1. Merges attributes from parameters
        $attributes && $this->fromArray($attributes);

        // 2.1 Loop and save collection records
        if ($this->coll) {
            $this->mapColl(__FUNCTION__);
            return $this;
        }

        // 2.2 Saves single record
        $isNew = $this->new;
        $primaryKey = $this->getPrimaryKey();

        // 2.2.2 Triggers before callbacks
        $this->triggerCallback('beforeSave');
        $this->triggerCallback($isNew ? 'beforeCreate' : 'beforeUpdate');

        if ($isNew) {
            $this->convertToDbValues();

            // 2.2.3.1 Inserts new record
            // Removes primary key value when it's empty to avoid SQL error
            if (array_key_exists($primaryKey, $this->attributes) && !$this->attributes[$primaryKey]) {
                unset($this->attributes[$primaryKey]);
            }

            $this->executeInsert($this->attributes);
            $this->new = false;
            $this->wasRecentlyCreated = true;

            // Receives primary key value when it's empty
            if (!isset($this->attributes[$primaryKey]) || !$this->attributes[$primaryKey]) {
                // Prepare sequence name for PostgreSQL
                $sequence = sprintf('%s_%s_seq', $this->db->getTable($this->getTable()), $primaryKey);
                $this->attributes[$primaryKey] = $this->db->lastInsertId($sequence);
                $this->setAttributeSource($primaryKey, static::ATTRIBUTE_SOURCE_DB);
            }
        } else {
            // 2.2.3.2 Updates existing record
            if ($attributes = $this->getUpdateAttributes()) {
                $this->executeUpdate($attributes, [$primaryKey => $this->attributes[$primaryKey]]);
            }
        }

        // 2.2.4 Reset changed attributes
        $this->resetChanges();

        // 2.2.5. Triggers after callbacks
        $this->triggerCallback($isNew ? 'afterCreate' : 'afterUpdate');
        $this->triggerCallback('afterSave');

        return $this;
    }

    /**
     * Delete the current record and trigger the beforeDestroy and afterDestroy callback
     *
     * @param int|string $id
     * @return $this
     * @svc
     */
    protected function destroy($id = null): self
    {
        $id && $this->find($id);

        if ($this->coll) {
            $this->mapColl(__FUNCTION__);
            return $this;
        }

        $this->triggerCallback('beforeDestroy');
        $result = $this->trigger('destroy');
        if (!$result) {
            $this->executeDestroy();
        }
        $this->triggerCallback('afterDestroy');

        return $this;
    }

    protected function executeDestroy()
    {
        $primaryKey = $this->getPrimaryKey();
        $this->executeDelete([$primaryKey => $this->attributes[$primaryKey]]);
        $this->new = true;
    }

    /**
     * Set the record field value
     *
     * @param string|int $name
     * @param mixed $value
     * @param bool $throwException
     * @return $this|false
     * @svc
     */
    protected function set($name, $value = null, bool $throwException = true)
    {
        if ($this->coll) {
            return $this->setCollValue($name, $value);
        }

        if ($this->hasColumn($name)) {
            return $this->setColumnValue($name, $value);
        }

        if ($this->hasVirtual($name)) {
            return $this->setVirtualValue($name, $value);
        }

        if ($throwException) {
            throw new InvalidArgumentException('Invalid property: ' . (null === $name ? '[null]' : $name));
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
    protected function find($id): ?self
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
    protected function findOrFail($id): self
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
    protected function findOrInit($id = null, $attributes = []): self
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
    protected function findOrCreate($id, $attributes = []): self
    {
        $this->findOrInit($id, $attributes);
        if ($this->isNew()) {
            $this->save();
        }
        return $this;
    }

    /**
     * @param array $attributes
     * @param array|object $data
     * @return $this
     * @svc
     */
    protected function findByOrCreate($attributes, $data = []): self
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
    protected function findAll(array $ids): self
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
    protected function findBy($column, $operator = null, $value = null): ?self
    {
        $this->coll = false;
        $data = $this->fetch(...func_get_args());
        if ($data) {
            $this->new = false;
            $this->setDbAttributes($data, true);
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
    protected function findAllBy($column, $operator = null, $value = null): self
    {
        $this->coll = true;
        $data = $this->fetchAll(...func_get_args());

        $records = [];
        foreach ($data as $key => $row) {
            $records[$key] = static::new([], [
                'wei' => $this->wei,
                'db' => $this->db,
                'table' => $this->getTable(),
                'new' => false,
            ])->setDbAttributes($row, true);
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
    protected function findOrInitBy(array $attributes, $data = []): self
    {
        if (!$this->findBy($attributes)) {
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
    protected function findByOrFail($column, $operator = null, $value = null): self
    {
        if ($this->findBy(...func_get_args())) {
            return $this;
        } else {
            throw new \Exception('Record not found', 404);
        }
    }

    /**
     * @param Req|null $req
     * @return $this
     * @throws \Exception
     * @svc
     */
    protected function findFromReq(Req $req = null): self
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
    protected function first(): ?self
    {
        return $this->findBy(null);
    }

    /**
     * @return $this|$this[]
     * @svc
     */
    protected function all(): self
    {
        return $this->findAllBy(null);
    }

    /**
     * Coll: Specifies a field to be the key of the fetched array
     *
     * @param string $column
     * @return $this
     * @svc
     */
    protected function indexBy(string $column): self
    {
        // Expect to work with coll
        if (!$this->coll) {
            $this->beColl();
        }

        $this->parentIndexBy($column);
        $this->attributes = $this->executeIndexBy($this->attributes, $column);
        return $this;
    }

    protected function execute()
    {
        $this->trigger('beforeExecute');
        return $this->parentExecute();
    }

    protected function addQueryPart($sqlPartName, $value, $append = false)
    {
        $this->trigger('beforeAddQueryPart', func_get_args());
        return $this->parentAddQueryPart($sqlPartName, $value, $append);
    }

    /**
     * Set db data to model
     *
     * @param array $attributes
     * @param bool $merge
     * @return $this
     */
    protected function setDbAttributes(array $attributes, bool $merge = false): self
    {
        $this->attributes = array_merge($merge ? $this->attributes : [], $attributes);
        $this->setAttributeSource('*', static::ATTRIBUTE_SOURCE_DB, true);
        return $this;
    }

    /**
     * Remove all change values  or the specified column change value
     *
     * @param string|null $column
     */
    protected function resetChanges(string $column = null): void
    {
        if ($column) {
            unset($this->changes[$column]);
        }
        $this->changes = [];
    }

    /**
     * Record the value of column before change
     *
     * @param string $column
     */
    protected function setChange(string $column): void
    {
        // Only record the init value of column
        // If column value change back to init value, it will be removed by the `removeUnchanged` method
        if (!array_key_exists($column, $this->changes)) {
            $this->changes[$column] = $this->getColumnValue($column);
        }
    }

    /**
     * Remove unchanged values
     *
     * ```php
     * $model = static::new(['column' => 'a']); // init column value to "a"
     * $model->column = 'b'; // $model->changes become ['column' => 'a']
     * $model->column = 'a'; // $model->changes still be ['column' => 'a']
     * $model->removeUnchanged(); // $model->changes become []
     * ```
     *
     * @param string|null $column
     * @return bool
     */
    protected function removeUnchanged(string $column = null): bool
    {
        if ($column) {
            if (!isset($this->changes[$column])) {
                return false;
            }

            $value = $this->getColumnValue($column);
            $original = $this->changes[$column];

            // If the value is an object, compare whether they have the same attributes and values,
            // and are instances of the same class.
            // @link https://www.php.net/manual/en/language.oop5.object-comparison.php
            if ($original === $value || (is_object($original) && $original == $value)) {
                unset($this->changes[$column]);
                return true;
            }
            return false;
        }

        $result = false;
        foreach ($this->changes as $column => $value) {
            if ($this->removeUnchanged($column)) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * Trigger a callback
     *
     * @param string $name
     */
    protected function triggerCallback(string $name): void
    {
        $this->trigger($name);
        $this->{$name}();
    }

    /**
     * @param array $columns
     * @return array
     */
    protected function getToArrayColumns(array $columns): array
    {
        if ($hidden = $this->getHidden()) {
            $columns = array_diff($columns, $hidden);
        }

        return $columns;
    }

    protected function virtualToArray(): array
    {
        $data = [];
        foreach ($this->virtual as $column) {
            $data[$column] = $this->{'get' . $this->camel($column) . 'Attribute'}();
        }

        return $data;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setColumnValue(string $name, $value): self
    {
        $this->setChange($name);

        $result = $this->callSetter($name, $value);
        if ($result) {
            $this->setAttributeSource($name, static::ATTRIBUTE_SOURCE_DB);
            return $this;
        }

        $this->attributes[$name] = $value;
        $this->setAttributeSource($name, static::ATTRIBUTE_SOURCE_USER);

        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function &getColumnValue(string $name)
    {
        $result = $this->callGetter($name, $value);
        if ($result) {
            return $value;
        }

        $source = $this->getAttributeSource($name);
        if (static::ATTRIBUTE_SOURCE_PHP === $source) {
            return $this->attributes[$name];
        }

        // Data flow: user => db => php

        // Convert user data to db data
        $value = $this->convertToDbValue($name);

        // Convert db data to php data
        $this->attributes[$name] = $this->trigger('getValue', [$value, $name]);
        $this->setAttributeSource($name, static::ATTRIBUTE_SOURCE_PHP);

        return $this->attributes[$name];
    }

    /**
     * @param string $name
     * @param int $source
     * @param bool $replace
     */
    protected function setAttributeSource(string $name, int $source, bool $replace = false): void
    {
        if ($replace) {
            $this->attributeSources = [$name => $source];
        } else {
            $this->attributeSources[$name] = $source;
        }
    }

    /**
     * Returns the attribute source of specified column name
     *
     * @param string $name
     * @return int
     */
    protected function getAttributeSource(string $name): int
    {
        return $this->attributeSources[$name] ?? $this->attributeSources['*'];
    }

    /**
     * Returns the service object
     *
     * @param string $name
     * @return Base
     * @throws \Exception
     */
    protected function &getServiceValue(string $name): Base
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
    protected function &getVirtualValue(string $name)
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
    protected function setVirtualValue(string $name, $value): self
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
     * @param mixed $name
     * @return bool
     */
    protected function hasVirtual($name): bool
    {
        return in_array($name, $this->virtual, true);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    protected function callGetter(string $name, &$value): bool
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
    protected function callSetter(string $name, $value): bool
    {
        $method = 'set' . $this->camel($name) . 'Attribute';
        if ($result = method_exists($this, $method)) {
            $this->{$method}($value);
        }
        return $result;
    }

    /**
     * Generates data for saving to database
     *
     * @return array
     */
    protected function convertToDbValues(): array
    {
        foreach ($this->attributes as $name => $value) {
            $this->convertToDbValue($name);
        }
        return $this->attributes;
    }

    /**
     * Convert the attribute value to database value
     *
     * @param string $column
     * @return mixed
     */
    protected function convertToDbValue(string $column)
    {
        $value = $this->attributes[$column] ?? null;

        if ($this->getAttributeSource($column) === static::ATTRIBUTE_SOURCE_DB) {
            return $value;
        }

        // Convert to db value by setter
        $result = $this->callSetter($column, $value);
        if ($result) {
            $this->setAttributeSource($column, static::ATTRIBUTE_SOURCE_DB);
            return $this->attributes[$column];
        }

        // Convert to db value by caster
        $this->attributes[$column] = $this->trigger('setValue', [$value, $column]);
        $this->setAttributeSource($column, static::ATTRIBUTE_SOURCE_DB);
        return $this->attributes[$column];
    }

    /**
     * Return the attribute values that should be update to database
     *
     * @return array
     */
    private function getUpdateAttributes(): array
    {
        $attributes = [];
        foreach ($this->changes as $column => $value) {
            // `removeUnchanged` will call `getColumnValue`, which may convert the USER value to a DB value,
            // and then convert the DB value to a PHP value, so here we call `convertToDbValue` in advance
            // to avoid converting the value to a DB value twice
            $attributes[$column] = $this->convertToDbValue($column);
            if ($this->removeUnchanged($column)) {
                unset($attributes[$column]);
            }
        }
        return $attributes;
    }

    private function baseName(): string
    {
        $parts = explode('\\', static::class);
        return end($parts);
    }

    private function pluralize(string $word): string
    {
        return wei()->str->pluralize($word);
    }

    /**
     * 获取当前类的服务名称对应的类
     *
     * @return string
     */
    private static function getServiceClass(): string
    {
        $wei = wei();
        return $wei->has($wei->getServiceName(static::class)) ?: static::class;
    }

    /**
     * @param array $conditions
     * @return int
     * @internal
     */
    private function executeDelete(array $conditions): int
    {
        return $this->db->delete($this->getTable(), $this->convertKeysToDbKeys($conditions));
    }

    /**
     * @param array $conditions
     * @return array
     * @internal
     */
    private function executeSelect(array $conditions): array
    {
        return $this->convertKeysToPhpKeys(
            $this->db->select($this->getTable(), $this->convertKeysToDbKeys($conditions)) ?: []
        );
    }

    /**
     * @param array $data
     * @return int
     * @internal
     */
    private function executeInsert(array $data): int
    {
        return $this->db->insert($this->getTable(), $this->convertKeysToDbKeys($data));
    }

    /**
     * @param array $data
     * @param array $conditions
     * @return int
     * @internal
     */
    private function executeUpdate(array $data, array $conditions): int
    {
        return $this->db->update(
            $this->getTable(),
            $this->convertKeysToDbKeys($data),
            $this->convertKeysToDbKeys($conditions)
        );
    }
}
