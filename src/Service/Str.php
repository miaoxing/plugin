<?php

namespace Miaoxing\Plugin\Service;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Miaoxing\Plugin\BaseService;

/**
 * 字符串操作服务
 *
 * @stable
 */
class Str extends BaseService
{
    protected static $snakeCache = [];

    protected static $camelCache = [];

    /**
     * @var Inflector
     */
    protected $inflector;

    /**
     * @param string $word
     * @return string
     */
    public function pluralize($word)
    {
        return $this->getInflector()->pluralize($word);
    }

    /**
     * @param string $word
     * @return string
     */
    public function singularize($word)
    {
        return $this->getInflector()->singularize($word);
    }

    /**
     * Convert a input to snake case
     *
     * @param string $input
     * @param string $delimiter
     * @return string
     */
    public function snake($input, $delimiter = '_')
    {
        if (isset(static::$snakeCache[$input][$delimiter])) {
            return static::$snakeCache[$input][$delimiter];
        }

        $value = $input;
        if (!ctype_lower($input)) {
            $value = strtolower(preg_replace('/(?<!^)[A-Z]/', $delimiter . '$0', $input));
        }

        return static::$snakeCache[$input][$delimiter] = $value;
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
     * Convert a input to dash case
     *
     * @param string $input
     * @return string
     */
    public function dash($input)
    {
        return $this->snake($input, '-');
    }

    /**
     * 获取对象的基础类名
     *
     * @param object|string $object
     * @return string
     */
    public function baseName($object)
    {
        $parts = explode('\\', is_string($object) ? $object : get_class($object));

        return end($parts);
    }

    /**
     * Get the inflector instance.
     *
     * @return Inflector
     */
    public function getInflector()
    {
        if (!$this->inflector) {
            $this->inflector = InflectorFactory::create()->build();
        }
        return $this->inflector;
    }
}
