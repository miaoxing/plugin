<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\BaseModelV2;
use Miaoxing\Plugin\Service\Request;

/**
 * @property Request $request
 */
trait ReqQueryTrait
{
    protected $joins = [];

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
            if (isset($this->joins[$relation])
                || !$this->request->has($relation)
                || !$this->hasRelation($relation)
            ) {
                continue;
            }

            $this->joins[$relation] = true;
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
            list($column, $value, $relation) = $this->parseReqColumn($name);
            if (!wei()->isPresent($value)) {
                continue;
            }

            if ($relation) {
                $this->reqJoin($relation);
            }
            $this->whereContains($column, $value);
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
        if ($this->getSqlPart('join')) {
            $prefix = $this->getTable() . '.';
        } else {
            $prefix = '';
        }

        foreach ((array) $columns as $column) {
            $min = $this->filterOutputColumn($column . '_min');
            if ($this->request->has($min)) {
                $this->andWhere($prefix . $column . ' >= ?', $this->request[$min]);
            }

            $max = $this->filterOutputColumn($column . '_max');
            if ($this->request->has($max)) {
                $this->andWhere($prefix . $column . ' <= ?', $this->processMaxDate($column, $this->request[$max]));
            }
        }

        return $this;
    }

    protected function processMaxDate($column, $value)
    {
        if (isset($this->casts[$column])
            && $this->casts[$column] == 'datetime'
            && wei()->isDate($value)
        ) {
            return $value . ' 23:59:59';
        }
        return $value;
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

    protected function parseReqColumn($column)
    {
        if (strpos($column, '.') === false) {
            // 查询当前表
            $value = $this->request[$column];
            $relation = null;

            // 有连表查询,加上表名
            if ($this->getSqlPart('join')) {
                $column = $this->getTable() . '.' . $column;
            }
        } else {
            // 查询关联表
            list($relation, $relationColumn) = explode('.', $column, 2);
            $value = $this->request[$relation][$relationColumn];
        }

        return [$column, $value, $relation];
    }
}
