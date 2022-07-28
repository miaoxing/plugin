<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Resource\BaseResource;
use Wei\ModelTrait as BaseModelTrait;
use Wei\Ret;

trait ModelTrait
{
    use BaseModelTrait;

    /**
     * 返回当前对象
     *
     * @return $this
     * @deprecated 改用 $wei->get($modelName) 返回模型实例
     * @internal 用于兼容已有逻辑，待删除
     */
    public function __invoke(): self
    {
        return $this;
    }

    /**
     * Returns the success result with model data
     *
     * @param array|string|BaseResource|mixed $merge
     * @return Ret
     * @svc
     */
    protected function toRet($merge = []): Ret
    {
        if (is_array($merge)) {
            $data = $merge;
        } elseif ($merge instanceof BaseResource) {
            $data = $merge->toArray($this);
        } elseif (is_subclass_of($merge, BaseResource::class)) {
            $data = $merge::toArray($this);
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Expected argument of type array, instance of BaseResource, or subclass of BaseReource, "%s" given',
                is_object($merge) ? get_class($merge) : gettype($merge)
            ));
        }

        if ($this->coll) {
            return $this->collToRet($data);
        } else {
            return $this->suc($data + ['data' => $this])->setMetadata('model', $this);
        }
    }
}
