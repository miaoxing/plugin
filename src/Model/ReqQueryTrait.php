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

    protected $reqMaps = [];

    /**
     * @param array|\ArrayAccess $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

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

        $req = $this->request->getData();
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
        $column = $this->filterInputColumn($name);
        if (!$this->hasColumn($column)) {
            return;
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

        /** @var BaseModelV2 $related */
        $related = $this->$relation();
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
        if (strpos($name, '$') === false) {
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
                return $this->andWhere($column . ' = ?', $value);

            case 'ct':
                return $this->whereContains($column, $value);

            case 'ge':
                return $this->andWhere($column . ' >= ?', $value);

            case 'le':
                return $this->andWhere($column . ' <= ?', $this->processMaxDate($column, $value));

            case 'gt':
                return $this->andWhere($column . ' > ?', $value);

            case 'lt':
                return $this->andWhere($column . ' < ?', $this->processMaxDate($column, $value));

            case 'hs':
                return $this->whereHas($column, $value);

            default:
                return $this;
        }
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
            $name = $related->getClassServiceName();
            $config = $this->relations[$name];

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

    public function sort($defaultColumn = 'id', $defaultOrder = 'DESC')
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

        if ($this->getSqlPart('join')) {
            $sort = $this->getTable() . '.' . $sort;
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

            /** @var BaseModelV2 $related */
            $related = $this->$relation();
            $column = $related->getTable() . '.' . $relationColumn;
        }

        return [$column, $value, $relation];
    }
}
