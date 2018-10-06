<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\BaseModelV2;
use Miaoxing\Plugin\Service\Request;

/**
 * @property Request $request
 */
trait QuickQueryTrait
{
    /**
     * @param array|\ArrayAccess $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    public function reqJoin($relations)
    {
        foreach ((array) $relations as $relation) {
            if (!$this->request->has($relation) || !$this->hasRelation($relation)) {
                continue;
            }

            $this->selectMain();

            /** @var BaseModelV2 $related */
            $related = $this->$relation();
            $config = $this->relations[$relation];

            $table = $related->getTable();

            // 处理跨数据库的情况
            if ($related->db != $this->db) {
                $table = $related->db->getDbname() . '.' . $table;
            }

            $this->leftJoin(
                $table,
                $table . '.' . $config['foreignKey'] . ' = ' . $this->getTable() . '.' . $config['localKey']
            );
        }

        return $this;
    }

    public function like($columns)
    {
        foreach ((array) $columns as $column) {
            $name = $this->filterOutputColumn($column);
            if (strpos($name, '.') !== false) {
                list($table, $column) = explode('.', $name, 2);
                $value = $this->request[$table][$column];
            } else {
                $value = $this->request[$name];
            }
            if (!wei()->isPresent($value)) {
                continue;
            }
            $this->andWhere($name . ' LIKE ?', '%' . $value . '%');
        }

        return $this;
    }

    public function equals($columns)
    {
        foreach ((array) $columns as $column) {
            $name = $this->filterOutputColumn($column);
            if ($this->request->has($name)) {
                $this->andWhere([$column => $this->request[$name]]);
            }
        }

        return $this;
    }

    public function between($columns)
    {
        foreach ((array) $columns as $column) {
            $min = $this->filterOutputColumn($column . '_min');
            if ($this->request->has($min)) {
                $this->andWhere($column . ' >= ?', $this->request[$min]);
            }

            $max = $this->filterOutputColumn($column . '_max');
            if ($this->request->has($max)) {
                $this->andWhere($column . ' <= ?', $this->request[$max]);
            }
        }

        return $this;
    }

    public function reqHas($columns)
    {
        foreach ((array) $columns as $column) {
            $name = $this->filterOutputColumn($column);
            if ($this->request->has($column)) {
                $this->whereHas($column, $this->request[$name]);
            }
        }

        return $this;
    }

    public function sort($defaultColumn = 'id', $defaultOrder = 'DESC', $tableName = false)
    {
        if ($this->request->has('sort')) {
            $name = $this->filterInputColumn($this->request['sort']);
            if (in_array($name, $this->getFields())) {
                $sort = $name;
            } else {
                $sort = $defaultColumn;
            }
        } else {
            $sort = $defaultColumn;
        }

        if ($this->request->has('order')) {
            $order = strtoupper($this->request['order']);
            if (!in_array($order, ['ASC', 'DESC'])) {
                $order = $defaultOrder;
            }
        } else {
            $order = $defaultOrder;
        }

        if ($tableName) {
            $sort = $this->fullTable . '.' . $sort;
        }

        $this->orderBy($sort, $order);

        return $this;
    }

    public function paginate()
    {
        $limit = $this->request['rows'] ?: 10;
        $page = $this->request['page'] ?: 1;

        $this->limit($limit)->page($page);

        return $this;
    }
}
