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
            if (isset($this->request[$name]) && $this->request[$name]) {
                $this->andWhere($column . ' LIKE ?', '%' . $this->request[$name] . '%');
            }
        }

        return $this;
    }

    public function equals($columns)
    {
        foreach ((array) $columns as $column) {
            $name = $this->filterOutputColumn($column);
            if (isset($this->request[$name]) && wei()->isPresent($this->request[$name])) {
                $this->andWhere([$column => $this->request[$name]]);
            }
        }

        return $this;
    }

    public function between($columns)
    {
        foreach ((array) $columns as $column) {
            $min = $this->filterOutputColumn($column . '_min');
            if (isset($this->request[$min]) && $this->request[$min]) {
                $this->andWhere($column . ' >= ?', $this->request[$min]);
            }

            $max = $this->filterOutputColumn($column . '_max');
            if (isset($this->request[$max]) && $this->request[$max]) {
                $this->andWhere($column . ' <= ?', $this->request[$max]);
            }
        }

        return $this;
    }

    public function sort($defaultColumn = 'id', $defaultOrder = 'DESC')
    {
        if (isset($this->request['sort']) && in_array($this->request['sort'], $this->getFields())) {
            $sort = $this->request['sort'];
        } else {
            $sort = $defaultColumn;
        }

        if (isset($this->request['order'])) {
            $order = strtoupper($this->request['order']);
            if (!in_array($order, ['ASC', 'DESC'])) {
                $order = $defaultOrder;
            }
        } else {
            $order = $defaultOrder;
        }

        $this->orderBy($sort, $order);

        return $this;
    }

    public function paginate()
    {
        $limit = isset($this->request['rows']) ?: 10;
        $page = isset($this->request['page']) ?: 1;

        $this->limit($limit)->page($page);

        return $this;
    }
}
