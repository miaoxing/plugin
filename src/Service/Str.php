<?php

namespace Miaoxing\Plugin\Service;

use Doctrine\Common\Inflector\Inflector;
use Miaoxing\Plugin\BaseService;

/**
 * 字符串操作服务
 */
class Str extends BaseService
{
    protected static $snakeCache = [];

    protected static $camelCache = [];

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
     * Convert a input to snake case
     *
     * @param string $input
     * @return string
     */
    public function snake($input)
    {
        if (isset(static::$snakeCache[$input])) {
            return static::$snakeCache[$input];
        }

        $value = $input;
        if (!ctype_lower($input)) {
            $value = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
        }

        return static::$snakeCache[$input] = $value;
    }

    /**
     * Convert a input to camel case
     *
     * @param string $input
     * @return string
     */
    public function camel($input)
    {
        if (isset(static::$camelCache[$input])) {
            return static::$camelCache[$input];
        }

        return static::$camelCache[$input] = lcfirst(str_replace(' ', '', ucwords(strtr($input, '_-', '  '))));
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
