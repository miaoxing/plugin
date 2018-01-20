<?php

namespace Miaoxing\Plugin\Service;

use Doctrine\Common\Inflector\Inflector;
use Miaoxing\Plugin\BaseService;

/**
 * 字符串操作服务
 */
class Str extends BaseService
{
    /**
     * @param string $word
     * @return string
     */
    public function pluralize($word)
    {
        return Inflector::pluralize($word);
    }

    /**
     * @param string $word
     * @return string
     */
    public function singularize($word)
    {
        return Inflector::singularize($word);
    }

    /**
     * 获取对象的基础类名
     *
     * @param object $object
     * @return string
     */
    public function baseName($object)
    {
        $parts = explode('\\', get_class($object));

        return end($parts);
    }
}
