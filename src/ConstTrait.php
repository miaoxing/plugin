<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Model\ReqQueryTrait;
use Wei\Request;

/**
 * @property Request $request
 * @todo 支持 name 之外 ？
 */
trait ConstTrait
{
    /**
     * @var array
     */
    protected static $consts = [];

    /**
     * @var array
     */
    protected static $constNameToIds = [];

    /**
     * @var array
     */
    protected $constExcludes = [
        'STATE_DIRTY',
        'STATE_CLEAN',
    ];

    /**
     * Get constants table by prefix
     *
     * @param string $prefix
     * @return array
     */
    public function getConsts($prefix)
    {
        if (isset(self::$consts[$prefix])) {
            return self::$consts[$prefix];
        }

        // 1. Get all class constants
        $class = new \ReflectionClass($this);
        $consts = $class->getConstants();

        // 2. Use exiting constant configs
        $property = lcfirst(str_replace('_', '', ucwords($prefix, '_'))) . 'Names';
        if (isset($this->{$property})) {
            $names = $this->{$property};
        } else {
            $names = [];
        }

        // 3. Generate id and name
        $prefix .= '_';
        $data = [];
        $length = strlen($prefix);
        foreach ($consts as $name => $id) {
            if (0 !== stripos($name, $prefix)) {
                continue;
            }
            if (in_array($name, $this->constExcludes, true)) {
                continue;
            }
            $data[$id]['id'] = $id;
            $data[$id]['key'] = strtolower(strtr(substr($name, $length), ['_' => '-']));
            if (isset($names[$id])) {
                $data[$id]['name'] = $names[$id];
            }
        }

        self::$consts[$prefix] = $data;

        return $data;
    }

    public function getConstName($prefix, $id)
    {
        return $this->getConsts($prefix)[$id]['name'];
    }

    public function getConstsWithAll($prefix)
    {
        $consts = $this->getConsts($prefix);
        array_unshift($consts, [
            'id' => '',
            'key' => 'all',
            'name' => '全部',
        ]);
        return $consts;
    }

    /**
     * 将请求的key(字母)转换为id(数字)
     *
     * 用法
     * $curStatus = wei()->xxx->getConstId('status', $req['status']);
     *
     * @param string $prefix
     * @param string $reqKey
     * @return string
     */
    public function getConstId($prefix, $reqKey)
    {
        $keyToIds = $this->getConstKeyToIds($prefix);
        if (isset($keyToIds[$reqKey])) {
            return $keyToIds[$reqKey];
        }

        return '';
    }

    /**
     * 获取key和id关联数组
     *
     * @param string $prefix
     * @return array
     */
    public function getConstKeyToIds($prefix)
    {
        return array_column($this->getConsts($prefix), 'id', 'key');
    }

    /**
     * 将请求的key(字母)转换为key并用于查询
     *
     * @param string $prefix
     * @param string $reqKey
     * @return $this
     */
    public function whereConstKey($prefix, $reqKey = null)
    {
        if (1 === func_num_args()) {
            $reqKey = $this->request->get($prefix);
        }

        $id = $this->getConstId($prefix, $reqKey);
        if ('' !== $id) {
            /* @see ReqQueryTrait::parseReqColumn */
            // @phpstan-ignore-next-line
            list($column) = $this->parseReqColumn($prefix);
            $this->where($column, $id);
        }

        return $this;
    }
}
