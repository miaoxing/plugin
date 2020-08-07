<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Service\Model;
use Wei\Req;

/**
 * @mixin \ReqMixin
 */
trait ReqQueryTrait
{
    protected $joins = [];

    protected $reqMaps = [];

    /**
     * @param Req $req
     * @return $this
     * @deprecated Use `setReq` instead
     */
    public function setRequest($req)
    {
        return $this->setReq($req);
    }

    /**
     * @param Req $request
     * @return $this
     */
    public function setReq($req)
    {
        $this->req = $req;
        return $this;
    }

    /**
     * 根据请求参数,自动执行查询
     *
     * @param array $options
     * @return $this
     */
    public function reqQuery(array $options = [])
    {
        // 允许传索引数组表示常见的only选项
        if (isset($options[0])) {
            $options['only'] = $options;
        }

        $req = $this->req->getData();
        if (isset($options['only'])) {
            $req = array_intersect_key($req, array_flip((array) $options['only']));
        }
        if (isset($options['except'])) {
            $req = array_diff_key($req, array_flip((array) $options['except']));
        }

        $isPresent = wei()->isPresent;
        foreach ($req as $name => $value) {
            if (!$isPresent($value)) {
                continue;
            }

            if (is_array($value)) {
                foreach ($value as $subName => $subValue) {
                    if (!$isPresent($subValue)) {
                        continue;
                    }

                    $this->processRelationQuery($name, $subName, $subValue);
                }
            } else {
                $this->processColumnQuery($name, $value);
            }
        }
        return $this;
    }

    /**
     * 指定请求值映射
     *
     * @param string $name
     * @param array $values
     * @return $this
     */
    public function reqMap($name, array $values)
    {
        $this->reqMaps[$name] = $values;
        return $this;
    }

    /**
     * @param array|string $relations
     * @return $this
     */
    public function reqJoin($relations)
    {
        foreach ((array) $relations as $relation) {
            if (isset($this->joins[$relation])
                || !$this->req->has($relation)
                || !$this->hasRelation($relation)
            ) {
                continue;
            }

            $this->joins[$relation] = true;
            $this->selectMain();

            /** @var Model $related */
            $related = $this->{$relation}();
            $name = $related->getClassServiceName();
            $config = $this->relations[$name];

            $table = $related->getTable();

            // 处理跨数据库的情况
            if ($related->db != $this->db) {
                $table = $related->db->getDbname() . '.' . $table;
            }

            $this->leftJoin(
                $table,
                $table . '.' . $config['foreignKey'],
                '=',
                $this->getTable() . '.' . $config['localKey']
            );
        }

        return $this;
    }

    public function equals($columns)
    {
        foreach ((array) $columns as $column) {
            $name = $this->convertOutputIdentifier($column);
            if ($this->req->has($name)) {
                $this->where($column, $this->req[$name]);
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
            $min = $this->convertOutputIdentifier($column . '_min');
            if ($this->req->has($min)) {
                $this->where($prefix . $column, '>=', $this->req[$min]);
            }

            $max = $this->convertOutputIdentifier($column . '_max');
            if ($this->req->has($max)) {
                $this->where($prefix . $column, '<=', $this->processMaxDate($column, $this->req[$max]));
            }
        }

        return $this;
    }

    public function reqHas($columns)
    {
        foreach ((array) $columns as $column) {
            $name = $this->convertOutputIdentifier($column);
            if ($this->req->has($name)) {
                $this->whereHas($column, $this->req[$name]);
            }
        }

        return $this;
    }

    public function sort($defaultColumn = 'id', $defaultOrder = 'DESC')
    {
        if ($this->req->has('sort')) {
            $name = $this->convertInputIdentifier($this->req['sort']);
            if (in_array($name, $this->getFields(), true)) {
                $sort = $name;
            } else {
                $sort = $defaultColumn;
            }
        } else {
            $sort = $defaultColumn;
        }

        if ($this->req->has('order')) {
            $order = strtoupper($this->req['order']);
            if (!in_array($order, ['ASC', 'DESC'], true)) {
                $order = $defaultOrder;
            }
        } else {
            $order = $defaultOrder;
        }

        if ($this->getSqlPart('join')) {
            $sort = $this->getTable() . '.' . $sort;
        }

        $this->orderBy($sort, $order);

        return $this;
    }

    public function paginate()
    {
        $limit = $this->req['rows'] ?: 10;
        $page = $this->req['page'] ?: 1;

        $this->limit($limit)->page($page);

        return $this;
    }

    /**
     * 查询当前模型的值
     *
     * @param string $name
     * @param mixed $value
     */
    protected function processColumnQuery($name, $value)
    {
        if (isset($this->reqMaps[$name][$value])) {
            $value = $this->reqMaps[$name][$value];
        }

        // 提取出操作
        list($name, $op) = $this->parseNameAndOp($name);

        // 检查字段是否存在
        $column = $this->convertInputIdentifier($name);
        if (!$this->hasColumn($column)) {
            return;
        }

        if ($this->getSqlPart('join')) {
            $column = $this->getTable() . '.' . $column;
        }

        $this->queryByOp($column, $op, $value);
    }

    /**
     * 查询关联模型的值
     *
     * @param string $relation
     * @param string $name
     * @param mixed $value
     */
    protected function processRelationQuery($relation, $name, $value)
    {
        if (!$this->hasRelation($relation)) {
            return;
        }

        $this->reqJoin($relation);

        list($name, $op) = $this->parseNameAndOp($name);

        /** @var Model $related */
        $related = $this->{$relation}();
        if (!$related->hasColumn($name)) {
            return;
        }

        $this->queryByOp($related->getTable() . '.' . $name, $op, $value);
    }

    /**
     * 从请求名称中解析出字段名称和操作符
     *
     * @param string $name
     * @return array
     */
    protected function parseNameAndOp($name)
    {
        if (false === strpos($name, '$')) {
            return [$name, 'eq'];
        } else {
            return explode('$', $name, 2);
        }
    }

    /**
     * 根据操作符执行查询
     *
     * @param string $column
     * @param string $op
     * @param mixed $value
     * @return $this
     */
    protected function queryByOp($column, $op, $value)
    {
        switch ($op) {
            case 'eq':
                return $this->where($column, '=', $value);

            case 'ct':
                return $this->whereContains($column, $value);

            case 'ge':
                return $this->where($column, '>=', $value);

            case 'le':
                return $this->where($column, '<=', $this->processMaxDate($column, $value));

            case 'gt':
                return $this->where($column, '>', $value);

            case 'lt':
                return $this->where($column, '<', $this->processMaxDate($column, $value));

            case 'hs':
                return $this->whereHas($column, $value);

            default:
                return $this;
        }
    }

    /**
     * @param array|string $columns
     * @return $this
     * @svc
     */
    protected function like($columns)
    {
        foreach ((array) $columns as $column) {
            $name = $this->convertOutputIdentifier($column);
            [$column, $value, $relation] = $this->parseReqColumn($name);
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

    protected function processMaxDate($column, $value)
    {
        if (isset($this->casts[$column])
            && 'datetime' == $this->casts[$column]
            && wei()->isDate($value)
        ) {
            return $value . ' 23:59:59';
        }
        return $value;
    }

    protected function parseReqColumn($column)
    {
        if (false === strpos($column, '.')) {
            // 查询当前表
            $value = $this->req[$column];
            $relation = null;

            // 有连表查询,加上表名
            if ($this->getSqlPart('join')) {
                $column = $this->getTable() . '.' . $column;
            }

            $column = $this->convertInputIdentifier($column);
        } else {
            // 查询关联表
            [$relation, $relationColumn] = explode('.', $column, 2);
            $value = $this->req[$relation][$relationColumn];

            /** @var Model $related */
            $related = $this->{$relation}();
            $column = $related->getTable() . '.' . $relationColumn;
        }

        return [$column, $value, $relation];
    }
}
