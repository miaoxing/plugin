<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Resource\BaseResource;
use Wei\ModelTrait as BaseModelTrait;
use Wei\Ret;

trait ModelTrait
{
    use BaseModelTrait;

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
