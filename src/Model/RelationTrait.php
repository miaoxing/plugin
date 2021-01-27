<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Model\Attributes\Relation;
use Miaoxing\Plugin\Service\Str;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * Add relation functions to the model
 *
 * @internal Expected to be used only by ModelTrait
 */
trait RelationTrait
{
    /**
     * The relation config
     *
     * @var array
     * @internal
     */
    protected $relation = [];

    /**
     * The loaded relation values
     *
     * @var array
     */
    protected $relationValues = [];

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
        $relationAttributes = [$this->relation['foreignKey'] => $this->relation['localValue']];

        if ($this->coll) {
            $this->all();
            $this->saveColl($attributes, $relationAttributes);
        } else {
            $this->findOrInitBy([])->fromArray($attributes)->save($relationAttributes);
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
        $localKey || $localKey = $this->getPrimaryKey();
        $foreignKey || $foreignKey = $this->getForeignKey();

        $localKey = $this->convertToPhpKey($localKey);
        $foreignKey = $this->convertToPhpKey($foreignKey);

        $value = $this->getRelationParams($localKey);
        $related->setRelation([
            'localKey' => $localKey,
            'foreignKey' => $foreignKey,
            'localValue' => $value,
        ]);

        $related->where($foreignKey, $value);

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
        $localKey || $localKey = Str::snake($related->getModelBaseName()) . '_' . $this->getPrimaryKey();

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

        $primaryKey = $this->getPrimaryKey();
        $junctionTable || $junctionTable = $this->getJunctionTable($related);
        $foreignKey || $foreignKey = $this->getForeignKey();
        $relatedKey || $relatedKey = Str::snake($related->getModelBaseName()) . '_' . $primaryKey;

        $related->setRelation([
            'junctionTable' => $junctionTable,
            'relatedKey' => $this->convertToPhpKey($relatedKey),
            'foreignKey' => $this->convertToPhpKey($foreignKey),
            'localKey' => $this->convertToPhpKey($primaryKey),
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
            $relation = $related->getRelation();

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
     * @return WeiBaseModel|WeiBaseModel[]
     */
    protected function loadHasMany(?WeiBaseModel $related, array $relation, string $name)
    {
        $coll = $related ? $related::newColl() : [];
        $data = $related ? $related->fetchAll() : [];
        $hasForeignKey = $related ? $related->hasColumn($relation['foreignKey']) : false;

        // An array containing model objects grouped by relational foreign keys
        $groupBy = [];

        foreach ($data as $row) {
            /** @var WeiBaseModel $model */
            $model = call_user_func([$related, 'new']);
            $coll[] = $model;
            $groupBy[$row[$relation['foreignKey']]][] = $model;

            // Remove external data
            if (!$hasForeignKey) {
                unset($row[$relation['foreignKey']]);
            }
            $model->setAttributesFromDb($row);
        }

        foreach ($this->attributes as $model) {
            $modelRelation = $related::newColl();
            $model->setRelationValue($name, $modelRelation);

            // NOTE: 从数据库取出为 string, 因此必须转换了再比较
            $localValue = (string) $model->getColumnValue($relation['localKey']);
            if (isset($groupBy[$localValue])) {
                $modelRelation->setAttributes($groupBy[$localValue]);
            }
        }

        return $coll;
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
     * Generate the foreign key name
     *
     * @return string
     */
    protected function getForeignKey(): string
    {
        return Str::snake($this->getModelBaseName()) . '_' . $this->getPrimaryKey();
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
        return $this->coll ? $this->relationParams : $this->getColumnValue($column);
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

        $relation = $related->getRelation();
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
     * @svc
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
     * @param array $relation
     * @return $this
     * @internal
     */
    protected function setRelation(array $relation): self
    {
        $this->relation = $relation;
        return $this;
    }

    /**
     * Return the relation config
     *
     * @return array
     * @internal
     */
    protected function getRelation(): array
    {
        return $this->relation;
    }
}
