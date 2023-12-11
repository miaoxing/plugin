<?php

namespace Miaoxing\Plugin\Resource;

use Miaoxing\Plugin\BaseService;
use Wei\BaseModel;

/**
 * Convert the model object to array for API response, reference from Laravel
 *
 * @method array transform($model) Convert the model object to array
 * @experimental 可能调整命名，更改静态方法为服务方法等
 * @link https://laravel.com/docs/8.x/eloquent-resources
 */
abstract class BaseResource extends BaseService
{
    /**
     * {@inheritdoc}
     */
    protected static $createNewInstance = true;

    /**
     * @var MissingValue|null
     */
    protected static $missingValue;

    /**
     * @var array
     */
    protected $includes = [];

    /**
     * Create a new resource object
     *
     * @return static
     */
    public static function new(): self
    {
        return new static();
    }

    /**
     * Convert the model object to array with wrapper and meta data
     *
     * @param BaseModel $model
     * @return array
     * @svc
     * @todo 支持 meta 等数据
     */
    protected function toArray(BaseModel $model): array
    {
        return [
            'data' => $this->transformData($model),
        ];
    }

    /**
     * Convert the model object to array
     *
     * @param \Wei\BaseModel $model
     * @return array
     * @svc
     */
    protected function transformData(BaseModel $model): array
    {
        $resource = $this;

        if (!$model->isColl()) {
            return $resource->filter($resource->transform($model), $model);
        }

        $data = [];
        foreach ($model as $item) {
            $data[] = $resource->filter($resource->transform($item), $item);
        }
        return $data;
    }

    /**
     * Transform the relation value when relation is loaded, or ignore the resource array key by condition
     *
     * @param BaseModel $model
     * @param string $relation
     * @return array|MissingValue
     */
    public static function transformDataWhenLoaded(BaseModel $model, string $relation)
    {
        // Loaded but may be null
        if ($model->isLoaded($relation) && $model->{$relation}) {
            return static::new()->transformData($model->{$relation});
        }
        return static::missingValue();
    }

    /**
     * Alias of `transformWhenLoaded`
     *
     * @param \Wei\BaseModel $model
     * @param string $relation
     * @return array|MissingValue
     */
    public static function whenLoaded(BaseModel $model, string $relation)
    {
        return static::transformDataWhenLoaded($model, $relation);
    }

    /**
     * Return the preset `MissingValue` object
     *
     * @return MissingValue
     */
    protected static function missingValue(): MissingValue
    {
        if (!static::$missingValue) {
            static::$missingValue = new MissingValue();
        }
        return static::$missingValue;
    }

    /**
     * Specify which include* methods to call to append data when transforming
     *
     * @param string|array $includes
     * @return $this
     * @svc
     */
    protected function includes($includes): self
    {
        $this->includes = (array) $includes;
        return $this;
    }

    /**
     * Remove missing value and expand merged value
     *
     * @param array $data
     * @param \Wei\BaseModel $model
     * @return array
     */
    protected function filter(array $data, BaseModel $model): array
    {
        $result = [];
        // TODO 调用了 merge 或 missing value 才需要循环检查
        foreach ($data as $key => $value) {
            if ($value instanceof MissingValue) {
                continue;
            }

            if ($value instanceof MergeValue) {
                $result = array_merge($result, $value->getValue());
                continue;
            }

            $result[$key] = $value;
        }

        foreach ($this->includes as $name) {
            $method = 'include' . ucfirst($name);
            if (!method_exists($this, $method)) {
                throw new \RuntimeException(sprintf('Method "%s" not found', $method));
            }
            $result[$name] = $this->{$method}($model);
        }

        return $result;
    }

    /**
     * Return the model array by specified columns that will be merge into the resource array
     *
     * @param \Wei\BaseModel $model
     * @param array $columns
     * @return MergeValue
     */
    protected function extract(BaseModel $model, array $columns): MergeValue
    {
        $data = [];
        foreach ($columns as $column) {
            // TODO 支持隐藏个别 $column => $this->when('xx')
            $data[$column] = $model->get($column);
        }
        return new MergeValue($data);
    }

    /**
     * Return the value or ignore the resource array key by condition
     *
     * @param bool $bool
     * @param mixed $value
     * @return mixed|MissingValue
     */
    protected function when(bool $bool, $value)
    {
        if (!$bool) {
            return static::missingValue();
        }

        return $value instanceof \Closure ? $value() : $value;
    }

    /**
     * Return an array that will be merged into the resource array, or ignore the value by condition
     *
     * @param bool $bool
     * @param array|\Closure $value
     * @return MergeValue|MissingValue
     */
    protected function mergeWhen(bool $bool, $value)
    {
        if (!$bool) {
            return static::missingValue();
        }

        return new MergeValue($value instanceof \Closure ? $value() : $value);
    }
}
