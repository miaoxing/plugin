<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseModelV2;
use Miaoxing\Plugin\BaseService;
use Miaoxing\Services\Service\Str;

/**
 * @property Str $str
 */
class Convention extends BaseService
{
    /**
     * @param object $object 可传入控制器或服务
     * @return string
     */
    public function getModelName($object)
    {
        $basename = lcfirst($this->str->baseName($object));
        $name = $this->str->singularize($basename);

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
