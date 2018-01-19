<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseModelV2;
use Miaoxing\Plugin\BaseService;

/**
 * @todo 更合适的名称
 */
class Convention extends BaseService
{
    /**
     * @param object $object 可传入控制器或服务
     * @return string
     */
    public function getModelName($object)
    {
        $parts = explode('\\', get_class($object));
        $basename = lcfirst(end($parts));

        // TODO 复数转单数
        // 如果是控制器,去掉复数
        $name = rtrim($basename, 's');

        return $name;
    }

    /**
     * @param object $object
     * @return BaseModelV2
     */
    public function createModel($object)
    {
        return $this->{$this->getModelName($object) . 'Model'}();
    }
}
