<?php

namespace Miaoxing\Plugin\Model;

use Wei\BaseModel;
use Wei\Req;

/**
 * @mixin \ReqMixin
 * @mixin \IsPresentMixin
 * @mixin \IsDateMixin
 * @experimental 待整理方法命名和参数
 */
trait ReqQueryTrait
{
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
     * The columns and orders that allowed to be sorted by request parameters
     *
     * eg:
     * [
     *   [['id'], ['DESC']],
     *   [['sort', 'created_at'], ['DESC', null]],
     * ]
     *
     * @var array|false
     */
    protected $reqOrderBy = [];

    /**
     * The conditions that can be used for search
     *
     * eg:
     * [
     *   'name',
     *   'age$gt',
     *   'profile' => [
     *     'city',
     *   ]
     * ]
     *
     * @var array|false
     */
    protected $reqSearch = [];

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
     * Set default sort column and optional sort direction
     *
     * @param string|array $column
     * @param string|array $order
     * @return $this
     */
    public function setDefaultSortColumn($column, $order = null): self
    {
        $this->defaultSortColumn = $column;
        func_num_args() > 1 && $this->setDefaultOrder($order);
        return $this;
    }

    /**
     * Set default sort direction
     *
     * @param string|array $order
     * @return $this
     */
    public function setDefaultOrder($order): self
    {
        $this->defaultOrder = $order;
        return $this;
    }

    /**
     * Set sortable columns and orders
     *
     * @param array|false $orderBy
     * @return $this
     */
    public function setReqOrderBy($orderBy): self
    {
        $this->reqOrderBy = $orderBy;
        return $this;
    }

    /**
     * Add one sortable columns and orders item
     *
     * @param string|array $orderByItem
     * @return $this
     */
    public function addReqOrderBy($orderByItem): self
    {
        if (!is_array($this->reqOrderBy)) {
            $this->reqOrderBy = [];
        }
        $this->reqOrderBy[] = (array) $orderByItem;
        return $this;
    }

    /**
     * Return sortable columns and orders
     *
     * @return array|false
     */
    public function getReqOrderBy()
    {
        if (false === $this->reqOrderBy) {
            return false;
        }

        foreach ($this->reqOrderBy as $i => &$item) {
            if (!is_array($item)) {
                $item = [[$item]];
                continue;
            }

            if (!isset($item[0])) {
                throw new \RuntimeException(
                    'Expected the order by value contains 0-index value, given: ' . json_encode($item)
                );
            }

            if (isset($item[0]) && !is_array($item[0])) {
                $item[0] = [$item[0]];
            }

            if (isset($item[1]) && !is_array($item[1])) {
                $item[1] = [$item[1]];
            }
        }
        return $this->reqOrderBy;
    }

    /**
     * Set the conditions that can be used for search
     *
     * @param array|false $reqSearch
     * @return $this
     */
    public function setReqSearch($reqSearch): self
    {
        $this->reqSearch = $reqSearch;
        return $this;
    }

    /**
     * Return the conditions that can be used for search
     *
     * @return array|false
     */
    public function getReqSearch()
    {
        return $this->reqSearch;
    }

    /**
     * Add paging, sorting and search query based on request parameters
     *
     * @return $this
     */
    public function reqQuery(): self
    {
        return $this->reqPage()->reqOrderBy()->reqSearch();
    }

    /**
     * Add paging query based on request parameters
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
     * Add sorting query based on request parameters
     *
     * @return $this
     */
    public function reqOrderBy(): self
    {
        if (false === $this->reqOrderBy) {
            return $this;
        }

        [$sortColumns, $orders] = $this->detectReqSortAndOrder();

        foreach ($sortColumns as $i => $column) {
            if (!$this->hasColumn($column)) {
                unset($sortColumns[$i], $orders[$i]);
            }
        }

        $hasJoin = $this->getQueryPart('join');
        foreach ($sortColumns as $i => $column) {
            if ($hasJoin) {
                $sort = $this->getTable() . '.' . $column;
            } else {
                $sort = $column;
            }

            $order = strtoupper($orders[$i] ?? 'DESC');
            if (!in_array($order, ['ASC', 'DESC'], true)) {
                $order = $this->defaultOrder;
            }

            $this->orderBy($sort, $order);
        }

        return $this;
    }

    /**
     * Add search query based on request parameters
     *
     * @return $this
     */
    public function reqSearch(): self
    {
        $reqSearch = $this->getReqSearch();
        if (false === $reqSearch) {
            return $this;
        }
        return $this->processReqSearch($this, (array) ($this->req['search'] ?? []), $reqSearch);
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
     * Add search query based on request parameters and allowed search conditions
     *
     * @param array $search
     * @param array $allows
     * @param BaseModel $model
     * @return $this
     */
    protected function processReqSearch(BaseModel $model, array $search, array $allows): self
    {
        foreach ($search as $name => $value) {
            if (!$this->isPresent($value)) {
                continue;
            }

            if (is_array($value) && $model->isRelation($name)) {
                $this->selectMain()->leftJoinRelation($name);
                $relation = $model->getRelationModel($name);
                $this->processReqSearch($relation, $value, $allows[$name] ?? []);
            }

            if ($allows) {
                $name = str_replace($this->reqSeparators, $this->reqSeparators[0], $name);
                if (!in_array($name, $allows, true)) {
                    continue;
                }
            }

            $this->addReqColumnQuery($model, $name, $value);
        }
        return $this;
    }

    /**
     * Add query based on model and column name
     *
     * @param BaseModel $model
     * @param string $name
     * @param mixed $value
     */
    protected function addReqColumnQuery(BaseModel $model, string $name, $value): void
    {
        [$name, $op] = $this->parseReqNameAndOp($name);
        if (!$model->hasColumn($name)) {
            return;
        }

        if ($model !== $this) {
            $name = $model->getTable() . '.' . $name;
        }

        if (isset($this->reqMaps[$name][$value])) {
            $value = $this->reqMaps[$name][$value];
        }

        $this->whereReqOp($name, $op, $value);
    }

    /**
     * Parse the column name and operator from the request name
     *
     * @param string $name
     * @return array
     */
    protected function parseReqNameAndOp(string $name): array
    {
        foreach ($this->reqSeparators as $separator) {
            if (false !== strpos($name, $separator)) {
                return explode($separator, $name, 2);
            }
        }
        return [$name, ''];
    }

    /**
     * Add a query based on the request operator
     *
     * @param string $column
     * @param string $op
     * @param mixed $value
     * @return $this
     */
    protected function whereReqOp(string $column, string $op, $value): self
    {
        switch ($op) {
            case '':
                return $this->where($column, '=', $value);

            case 'ct':
                return $this->whereContains($column, $value);

            case 'ge':
                return $this->where($column, '>=', $value);

            case 'le':
                return $this->where($column, '<=', $this->processReqDate($column, $value));

            case 'gt':
                return $this->where($column, '>', $value);

            case 'lt':
                return $this->where($column, '<', $this->processReqDate($column, $value));

            case 'hs':
                return $this->whereHas($column, $value);

            default:
                return $this;
        }
    }

    /**
     * Add "23:59:59" to the date value
     *
     * @param string $column
     * @param mixed $value
     * @return mixed
     */
    protected function processReqDate(string $column, $value)
    {
        if ('datetime' === $this->getColumnCast($column) && $this->isDate($value)) {
            return $value . ' 23:59:59';
        }
        return $value;
    }

    /**
     * Detect the request sort columns and orders
     *
     * @return array[]
     */
    protected function detectReqSortAndOrder(): array
    {
        $sortColumns = (array) $this->req['sort'];
        $orders = (array) $this->req['order'];

        if ($this->reqOrderBy) {
            $orderBy = $this->getReqOrderBy();
            $match = false;
            foreach ($orderBy as $item) {
                if (
                    $this->isArrayStartWiths($item[0], $sortColumns)
                    && $this->isOrderContains($item[1], $orders, count($sortColumns))
                ) {
                    $match = true;
                    break;
                }
            }
            if (!$match) {
                $sortColumns = [];
                $orders = [];
            }
        }

        return [
            $sortColumns ?: (array) $this->defaultSortColumn,
            $orders ?: (array) $this->defaultOrder,
        ];
    }

    private function isArrayStartWiths($arr1, $arr2): bool
    {
        if ($arr1 === $arr2) {
            return true;
        }

        if (count($arr1) < count($arr2)) {
            return false;
        }

        return array_slice($arr1, 0, count($arr2)) === $arr2;
    }

    private function isOrderContains(array $allows, array $reqs, int $length): bool
    {
        // 没有排序限制，返回包含
        if (!$allows) {
            return true;
        }

        // 补齐请求的排序方向，以便逐个判断
        $reqs = array_pad($reqs, $length, 'DESC');

        foreach ($reqs as $i => $req) {
            // 没有排序限制，跳过去检查下一个
            if (!isset($allows[$i]) || !$allows[$i]) {
                continue;
            }

            // 有排序限制，必须完全一样，否则不通过
            if (strtoupper($allows[$i]) !== strtoupper($req)) {
                return false;
            }
        }
        return true;
    }
}
