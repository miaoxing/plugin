<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Model\Attributes\Relation;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * Add relation functions to the model
 */
trait RelationTrait
{
    /**
     * The relation configs
     *
     * @var array
     */
    protected $relations = [];

    /**
     * The loaded relation values
     *
     * @var array
     */
    protected $relationValues = [];

    /**
     * The relation attributes to be save with current model
     *
     * @var array
     */
    protected $relationAttributes = [];

    /**
     * The parameter values for the relation base query
     *
     * @var mixed
     */
    protected $relationParams;

    /**
     * The relations that the current object has loaded
     *
     * @var array
     */
    protected $loadedRelations = [];

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
    public function saveRelation(array $attributes = []): self
    {
        if ($this->coll) {
            $this->all();
            $this->saveColl($attributes, $this->relationAttributes);
        } else {
            $this->findOrInitBy([])->fromArray($attributes)->save($this->relationAttributes);
        }
        return $this;
    }

    /**
     * @param self|string $model
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return WeiBaseModel
     */
    public function hasOne($model, $foreignKey = null, $localKey = null): WeiBaseModel
    {
        $related = $this->instanceRelationModel($model);
        $name = $related->getClassServiceName();

        $localKey || $localKey = $this->getPrimaryKey();
        $foreignKey || $foreignKey = $this->getForeignKey();
        $this->setRelation($name, ['foreignKey' => $foreignKey, 'localKey' => $localKey]);

        $value = $this->getRelationParams($localKey);
        $related->where($foreignKey, $value);
        $related->setRelationAttribute($foreignKey, $value);

        return $related;
    }

    /**
     * @param self|string $model
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return WeiBaseModel
     */
    public function hasMany($model, $foreignKey = null, $localKey = null): WeiBaseModel
    {
        return $this->hasOne($model, $foreignKey, $localKey)->beColl();
    }

    /**
     * @param self|string $model
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @return WeiBaseModel
     */
    public function belongsTo($model, $foreignKey = null, $localKey = null): WeiBaseModel
    {
        $related = $this->instanceRelationModel($model);
        $foreignKey || $foreignKey = $this->getPrimaryKey();
        $localKey || $localKey = $this->snake($related->getClassServiceName()) . '_' . $this->getPrimaryKey();

        return $this->hasOne($related, $foreignKey, $localKey);
    }

    /**
     * @param self|string $model
     * @param string|null $junctionTable
     * @param string|null $foreignKey
     * @param string|null $relatedKey
     * @return WeiBaseModel
     */
    public function belongsToMany($model, $junctionTable = null, $foreignKey = null, $relatedKey = null): WeiBaseModel
    {
        $related = $this->instanceRelationModel($model);
        $name = $this->getClassServiceName($related);

        $primaryKey = $this->getPrimaryKey();
        $junctionTable || $junctionTable = $this->getJunctionTable($related);
        $foreignKey || $foreignKey = $this->getForeignKey();
        $relatedKey || $relatedKey = $this->snake($name) . '_' . $primaryKey;
        $this->setRelation($name, [
            'junctionTable' => $junctionTable,
            'relatedKey' => $relatedKey,
            'foreignKey' => $foreignKey,
            'localKey' => $primaryKey,
        ]);

        $relatedTable = $related->getTable();
        $related->select($relatedTable . '.*')
            ->where([$junctionTable . '.' . $foreignKey => $this->getRelationParams($primaryKey)])
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
    public function load($names): self
    {
        $this->ensureColl();

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

            // 2. Fetch relation model data
            $ids = $this->getAll($relation['localKey']);
            $ids = array_unique(array_filter($ids));
            if ($ids) {
                $this->relationParams = $ids;
                $related = $this->{$name}();
                $this->relationParams = null;
            } else {
                $related = null;
            }

            // 3. Load relation data
            if (isset($relation['junctionTable'])) {
                $models = $this->loadBelongsToMany($related, $relation, $name);
            } elseif ($isColl) {
                $models = $this->loadHasMany($related, $relation, $name);
            } else {
                $models = $this->loadHasOne($related, $relation, $name);
            }

            // 4. Load nested relations
            if ($next && $models) {
                $models->load($next);
            }

            $this->loadedRelations[$name] = true;
        }

        return $this;
    }

    /**
     * Convert relation values to array
     *
     * @return array
     */
    protected function relationToArray(): array
    {
        $data = [];
        foreach ($this->relationValues as $name => $value) {
            $data[$name] = $value ? $value->toArray() : null;
        }
        return $data;
    }

    /**
     * @param WeiBaseModel|null $related
     * @param array $relation
     * @param string $name
     * @return $this|$this[]
     */
    protected function loadHasOne(?WeiBaseModel $related, array $relation, string $name)
    {
        if ($related) {
            $models = $related->all()->indexBy($relation['foreignKey']);
        } else {
            $models = [];
        }

        /** @var static $row */
        foreach ($this->attributes as $row) {
            $row->setRelationValue($name, $models[$row[$relation['localKey']]] ?? null);
        }
        return $models;
    }

    /**
     * @param WeiBaseModel|null $related
     * @param array $relation
     * @param string $name
     * @return $this|$this[]
     */
    protected function loadHasMany(?WeiBaseModel $related, array $relation, string $name)
    {
        $models = $related ? $related->fetchAll() : [];
        foreach ($this->attributes as $row) {
            $rowRelation = $related::newColl();
            $row->setRelationValue($name, $rowRelation);
            foreach ($models as $model) {
                // NOTE: 从数据库取出为 string, 因此必须转换了再比较
                if ($model[$relation['foreignKey']] === (string) $row[$relation['localKey']]) {
                    // Remove external data
                    if (!$related->hasColumn($relation['foreignKey'])) {
                        unset($model[$relation['foreignKey']]);
                    }
                    $rowRelation[] = forward_static_call([$related, 'new'])->setAttributes($model);
                }
            }
        }

        return $models;
    }

    /**
     * @param WeiBaseModel|null $related
     * @param array $relation
     * @param string $name
     * @return $this|$this[]
     */
    protected function loadBelongsToMany(?WeiBaseModel $related, array $relation, string $name)
    {
        if ($related) {
            $related->select($relation['junctionTable'] . '.' . $relation['foreignKey']);
        }

        return $this->loadHasMany($related, $relation, $name);
    }

    /**
     * @param WeiBaseModel|null $object
     * @return string
     */
    protected function getClassServiceName(WeiBaseModel $object = null): string
    {
        !$object && $object = $this;
        $parts = explode('\\', get_class($object));
        $name = lcfirst(end($parts));

        if ('Model' == substr($name, -5)) {
            $name = substr($name, 0, -5);
        }

        return $name;
    }

    /**
     * Generate the foreign key name
     *
     * @return string
     */
    protected function getForeignKey(): string
    {
        return $this->snake($this->getClassServiceName($this)) . '_' . $this->getPrimaryKey();
    }

    /**
     * Generate the junction table name
     *
     * @param WeiBaseModel $related
     * @return string
     */
    protected function getJunctionTable(WeiBaseModel $related): string
    {
        /** @var ModelTrait $related */
        $tables = [$this->getTable(), $related->getTable()];
        sort($tables);

        return implode('_', $tables);
    }

    /**
     * Return the parameter values for the relation base query
     *
     * @param string $column
     * @return mixed
     */
    protected function getRelationParams(string $column)
    {
        return $this->relationParams ?: (array_key_exists($column, $this->attributes) ? $this->get($column) : null);
    }

    /**
     * Create a relation model object with the model name or class name specified by the user,
     * or return the parameter if the parameter is a model object
     *
     * @param object|string $model
     * @return WeiBaseModel
     */
    protected function instanceRelationModel($model): WeiBaseModel
    {
        if ($model instanceof WeiBaseModel) {
            return $model;
        } elseif (is_subclass_of($model, WeiBaseModel::class)) {
            return forward_static_call([$model, 'new']);
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Expected "model" argument to be a subclass or an instance of WeiBaseModel, "%s" given',
                is_object($model) ? get_class($model) : (is_string($model) ? $model : gettype($model))
            ));
        }
    }

    /**
     * Call the relation method to receive the relation model object,
     * if the relation method does not exist, or the specified method is not a relation method,
     * an exception will be thrown
     *
     * @param string $name
     * @param bool $throw
     * @return WeiBaseModel|null
     */
    protected function getRelationModel(string $name, bool $throw = true): ?WeiBaseModel
    {
        // Ignore parent method
        if (method_exists(ModelTrait::class, $name)) {
            return null;
        }

        if (!method_exists($this, $name)) {
            return null;
        }

        // WARNING: Do not pass any untrusted names to avoid being attacked
        /** @var static $related */
        $related = $this->{$name}();

        if (!$related instanceof WeiBaseModel) {
            if ($throw) {
                throw new \LogicException(sprintf(
                    'Expected method "%s" to return an instance of WeiBaseModel, but returns "%s"',
                    $name,
                    is_object($related) ? get_class($related) : gettype($related)
                ));
            } else {
                return null;
            }
        }

        return $related;
    }

    /**
     * Load and return the relation value
     *
     * @param string $name
     * @param bool $exists
     * @param bool $throw
     * @return WeiBaseModel|null
     */
    protected function &getRelationValue(string $name, bool &$exists = null, bool $throw = true): ?WeiBaseModel
    {
        $exists = true;
        if (array_key_exists($name, $this->relationValues)) {
            return $this->relationValues[$name];
        }

        $related = $this->getRelationModel($name, $throw);
        if (!$related) {
            $exists = false;
            return $related;
        }

        $serviceName = $this->getClassServiceName($related);
        $relation = $this->relations[$serviceName];
        $localValue = $this[$relation['localKey']];

        if ($related->isColl()) {
            if ($localValue) {
                $this->setRelationValue($name, $related->all());
            } else {
                $this->setRelationValue($name, $related);
            }
        } else {
            if ($localValue) {
                $this->setRelationValue($name, $related->first() ?: null);
            } else {
                $this->setRelationValue($name, null);
            }
        }

        return $this->relationValues[$name];
    }

    /**
     * Set relation value
     *
     * @param string $name
     * @param WeiBaseModel|null $value
     * @return $this
     */
    protected function setRelationValue(string $name, ?WeiBaseModel $value): self
    {
        $this->relationValues[$name] = $value;
        return $this;
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
     * @scv
     */
    protected function isRelation(string $method): bool
    {
        try {
            $ref = new \ReflectionMethod($this, $method);
        } catch (\ReflectionException $e) {
            return false;
        }

        if (PHP_MAJOR_VERSION >= 8 && $ref->getAttributes(Relation::class)) {
            return true;
        }

        // Compat with PHP less than 8
        return strpos($ref->getDocComment() ?: '', '@Relation') !== false;
    }

    /**
     * Set the relation config
     *
     * @param string $name
     * @param array $relation
     * @return $this
     * @internal
     */
    protected function setRelation(string $name, array $relation): self
    {
        $this->relations[$name] = array_map([$this, 'convertToPhpKey'], $relation);
        return $this;
    }

    /**
     * Set the relation attributes to be save with current model
     *
     * @param string $column
     * @param mixed $value
     * @return $this
     */
    protected function setRelationAttribute(string $column, $value): self
    {
        $this->relationAttributes[$this->convertToPhpKey($column)] = $value;
        return $this;
    }
}
