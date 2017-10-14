<?php

namespace Miaoxing\Plugin\Traits;

trait ModelQuery
{
    protected $queryParams = [];

    public function setQueryParams($queryParams)
    {
        $this->queryParams = $queryParams;

        return $this;
    }

    public function like($columns)
    {
        foreach ((array) $columns as $column) {
            if (isset($this->queryParams[$column]) && $this->queryParams[$column]) {
                $this->andWhere($column . ' LIKE ?', '%' . $this->queryParams[$column] . '%');
            }
        }

        return $this;
    }

    public function equals($columns)
    {
        foreach ((array) $columns as $column) {
            if (isset($this->queryParams[$column]) && wei()->isPresent($this->queryParams[$column])) {
                $this->andWhere([$column => $this->queryParams[$column]]);
            }
        }

        return $this;
    }

    public function between($columns)
    {
        foreach ((array) $columns as $column) {
            $min = $column . '_min';
            if (isset($this->queryParams[$min]) && $this->queryParams[$min]) {
                $this->andWhere($column . ' >= ?', $this->queryParams[$min]);
            }

            $max = $column . '_max';
            if (isset($this->queryParams[$max]) && $this->queryParams[$max]) {
                $this->andWhere($column . ' <= ?', $this->queryParams[$max]);
            }
        }

        return $this;
    }

    public function sort($defaultColumn = 'id', $defaultOrder = 'DESC')
    {
        if (isset($this->queryParams['sort']) && in_array($this->queryParams['sort'], $this->getFields())) {
            $sort = $this->queryParams['sort'];
        } else {
            $sort = $defaultColumn;
        }

        if (isset($this->queryParams['order'])) {
            $order = strtoupper($this->queryParams['order']);
            if (!in_array($order, ['ASC', 'DESC'])) {
                $order = $defaultOrder;
            }
        } else {
            $order = $defaultOrder;
        }

        $this->orderBy($sort, $order);

        return $this;
    }
}
