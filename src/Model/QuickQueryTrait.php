<?php

namespace Miaoxing\Plugin\Model;

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

    public function like($columns)
    {
        foreach ((array) $columns as $column) {
            $name = $this->filterOutputColumn($column);
            if ($this->request->has($name)) {
                $this->andWhere($column . ' LIKE ?', '%' . $this->request[$name] . '%');
            }
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
