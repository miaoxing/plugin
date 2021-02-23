<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\BaseModel;
use Wei\Req;

/**
 * @mixin \ReqMixin
 * @mixin \IsPresentMixin
 * @property Req $req 需加上 phpstan 才能识别
 * @experimental 待整理方法命名和参数
 */
trait ReqQueryTrait
{
    protected $reqJoins = [];

    protected $reqMaps = [];

    /**
     * The strings used to separate search name and search type
     *
     * @var string[]
     */
    protected $reqSeparators = [':', '$'];

    /**
     * The default sort column name
     *
     * @var string|array
     */
    protected $defaultSortColumn = 'id';

    /**
     * The default sort direction, optional values are "ASC" and "DESC"
     *
     * @var string|array
     */
    protected $defaultOrder = 'DESC';

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
     * 根据请求参数，执行分页，排序和搜索操作
     *
     * @return $this
     */
    public function reqQuery(): self
    {
        return $this->reqPage()->reqOrderBy()->reqSearch();
    }

    /**
     * 根据请求参数，执行搜索操作
     *
     * @param array $options
     * @return $this
     */
    public function reqSearch(array $options = []): self
    {
        // 允许传索引数组表示常见的only选项
        if (isset($options[0])) {
            $options['only'] = $options;
        }

        $req = (array) $this->req->getData()['search'] ?? [];
        if (isset($options['only'])) {
            $req = array_intersect_key($req, array_flip((array) $options['only']));
        }
        if (isset($options['except'])) {
            $req = array_diff_key($req, array_flip((array) $options['except']));
        }

        $isPresent = $this->isPresent;
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
     * 根据请求参数，执行排序操作
     *
     * @return $this
     */
    public function reqOrderBy(): self
    {
        $sortColumns = (array) ($this->req['sort'] ?: $this->defaultSortColumn);
        $orders = (array) $this->req['order'];

        foreach ($sortColumns as $i => $column) {
            if (!$this->hasColumn($column)) {
                unset($sortColumns[$i]);
                unset($orders[$i]);
            }
        }

        $hasJoin = $this->getQueryPart('join');
        foreach ($sortColumns as $i => $column) {
            if ($hasJoin) {
                $sort = $this->getTable() . '.' . $column;
            } else {
                $sort = $column;
            }

            $order = strtoupper($orders[$i] ?? $this->defaultOrder);
            if (!in_array($order, ['ASC', 'DESC'], true)) {
                $order = $this->defaultOrder;
            }

            $this->orderBy($sort, $order);
        }

        return $this;
    }

    /**
     * 根据请求参数，执行分页操作
     *
     * @return $this
     */
    public function reqPage(): self
    {
        $limit = $this->req['limit'] ?: 10;
        $page = $this->req['page'] ?: 1;
        $this->limit($limit)->page($page);
        return $this;
    }

    /**
     * 指定请求值映射
     *
     * @param string $name
     * @param array $values
     * @return $this
     */
    public function reqMap(string $name, array $values): self
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
     * @param array|string $relations
     * @return $this
     */
    protected function reqJoin($relations)
    {
        foreach ((array) $relations as $relation) {
            if (isset($this->reqJoins[$relation])
                || !$this->isRelation($relation)
            ) {
                continue;
            }

            $this->reqJoins[$relation] = true;
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
