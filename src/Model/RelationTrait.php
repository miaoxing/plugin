<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * Add relation functions to the model
 *
 * Trait RelationTrait
 * @package Miaoxing\Plugin\Model
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
     * Extra data for saveRelation method
     *
     * @var array
     * @internal may be rename to avoid confuse with relationValues
     */
    protected $relationAttributes = [];

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
        if ($this->coll) {
            $this->all();
            $this->saveColl($attributes, $this->relationAttributes);
        } else {
            $this->findOrInitBy([])->fromArray($attributes)->save($this->relationAttributes);
        }

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
     * @param string $name
     * @param array $relation
     * @internal
     */
    private function addRelation($name, $relation)
    {
        $this->relations[$name] = array_map([$this, 'convertToPhpKey'], $relation);
    }
}
