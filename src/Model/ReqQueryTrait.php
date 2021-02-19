<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\BaseModel;
use Wei\Req;

/**
 * @mixin \ReqMixin
 * @property Req $req 需加上 phpstan 才能识别
 */
trait ReqQueryTrait
{
    protected $joins = [];

    protected $reqMaps = [];

    /**
     * The strings used to separate search name and search type
     *
     * @var string[]
     */
    protected $reqSeparators = [':', '$'];

    /**
     * @param Req $req
     * @return $this
     */
    public function setReq($req): self
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
    public function reqQuery(array $options = []): self
    {
        // 允许传索引数组表示常见的only选项
        if (isset($options[0])) {
            $options['only'] = $options;
        }

        $req = (array) $this->req->getData()['search'] ?: [];
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
    public function reqMap(string $name, array $values)
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
                || !$this->isRelation($relation)
            ) {
                continue;
            }

            $this->joins[$relation] = true;
            $this->selectMain();

            /** @var BaseModel $related */
            $related = $this->{$relation}();
            $config = $related->getRelation();

            $table = $related->getTable();

            // 处理跨数据库的情况
            if ($related->getDb() !== $this->getDb()) {
                $table = $related->getDb()->getDbname() . '.' . $table;
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

    /**
     * @param string|array $columns
     * @return $this
     */
    public function equals($columns): self
    {
        foreach ((array) $columns as $column) {
            if ($this->req->has($column)) {
                $this->where($column, $this->req[$column]);
            }
        }

        return $this;
    }

    /**
     * @param string|array $columns
     * @return $this
     */
    public function between($columns): self
    {
        if ($this->getQueryPart('join')) {
            $prefix = $this->getTable() . '.';
        } else {
            $prefix = '';
        }

        foreach ((array) $columns as $column) {
            // 支持数组形式
            if ($this->req->has($column) && is_array($this->req[$column])) {
                if (isset($this->req[$column][0])) {
                    $this->where($prefix . $column, '>=', $this->req[$column][0]);
                }
                if (isset($this->req[$column][1])) {
                    $this->where($prefix . $column, '<=', $this->req[$column][1]);
                }
                continue;
            }

            // 或是两个字段
            $min = $column . 'Min';
            if ($this->req->has($min)) {
                $this->where($prefix . $column, '>=', $this->req[$min]);
            }

            $max = $column = 'Max';
            if ($this->req->has($max)) {
                $this->where($prefix . $column, '<=', $this->processMaxDate($column, $this->req[$max]));
            }
        }

        return $this;
    }

    /**
     * @param string|array $columns
     * @return $this
     */
    public function reqHas($columns): self
    {
        foreach ((array) $columns as $column) {
            if ($this->req->has($column)) {
                $this->whereHas($column, $this->req[$column]);
            }
        }

        return $this;
    }

    /**
     * @param string $defaultColumn
     * @param string $defaultOrder
     * @return $this
     */
    public function sort(string $defaultColumn = 'id', string $defaultOrder = 'DESC')
    {
        if ($this->req->has('sort')) {
            $name = $this->req['sort'];
            if ($this->hasColumn($name)) {
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

        if ($this->getQueryPart('join')) {
            $sort = $this->getTable() . '.' . $sort;
        }

        $this->orderBy($sort, $order);

        return $this;
    }

    /**
     * @return $this
     */
    public function paginate(): self
    {
        $limit = $this->req['limit'] ?: 10;
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
    protected function processColumnQuery(string $name, $value): void
    {
        if (isset($this->reqMaps[$name][$value])) {
            $value = $this->reqMaps[$name][$value];
        }

        // 提取出操作
        list($name, $op) = $this->parseNameAndOp($name);

        // 检查字段是否存在
        if (!$this->hasColumn($name)) {
            return;
        }

        if ($this->getQueryPart('join')) {
            $name = $this->getTable() . '.' . $name;
        }

        $this->queryByOp($name, $op, $value);
    }

    /**
     * 查询关联模型的值
     *
     * @param string $relation
     * @param string $name
     * @param mixed $value
     */
    protected function processRelationQuery(string $relation, string $name, $value): void
    {
        if (!$this->isRelation($relation)) {
            return;
        }

        $this->reqJoin($relation);

        list($name, $op) = $this->parseNameAndOp($name);

        /** @var BaseModel $related */
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
    protected function parseNameAndOp(string $name): array
    {
        foreach ($this->reqSeparators as $separator) {
            if (false !== strpos($name, $separator)) {
                return explode($separator, $name, 2);
            }
        }
        return [$name, 'eq'];
    }

    /**
     * 根据操作符执行查询
     *
     * @param string $column
     * @param string $op
     * @param mixed $value
     * @return $this
     */
    protected function queryByOp(string $column, string $op, $value): self
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
    protected function like($columns): self
    {
        foreach ((array) $columns as $column) {
            [$column, $value, $relation] = $this->parseReqColumn($column);
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

    protected function processMaxDate($column, $value): string
    {
        if ('datetime' === $this->getColumnCast($column) && wei()->isDate($value)) {
            return $value . ' 23:59:59';
        }
        return $value;
    }

    protected function parseReqColumn(string $column): array
    {
        if (false === strpos($column, '.')) {
            // 查询当前表
            $value = $this->req[$column];
            $relation = null;

            // 有连表查询,加上表名
            if ($this->getQueryPart('join')) {
                $column = $this->getTable() . '.' . $column;
            }
        } else {
            // 查询关联表
            [$relation, $relationColumn] = explode('.', $column, 2);
            $value = $this->req[$relation][$relationColumn];

            /** @var BaseModel $related */
            $related = $this->{$relation}();
            $column = $related->getTable() . '.' . $relationColumn;
        }

        return [$column, $value, $relation];
    }
}
