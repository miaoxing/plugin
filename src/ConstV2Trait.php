<?php

namespace Miaoxing\Plugin;

trait ConstV2Trait
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
        if (isset($this->$property)) {
            $names = $this->$property;
        } else {
            $names = [];
        }

        // 3. Generate id and name
        $prefix .= '_';
        $data[] = [];
        $length = strlen($prefix);
        foreach ($consts as $name => $id) {
            if (stripos($name, $prefix) !== 0) {
                continue;
            }
            if (in_array($name, $this->constExcludes)) {
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

    public function getConstsWithAll($prefix)
    {
        $consts = $this->getConsts($prefix);
        array_unshift($consts, [
            'id' => '',
            'key' => 'all',
            'name' => '全部'
        ]);
        return $consts;
    }

    public function getConstId($prefix, $reqStatus)
    {
        $consts = $this->getConsts($prefix);
        $keys = array_column($consts, 'id', 'key');
        if (isset($keys[$reqStatus])) {
            return $keys[$reqStatus];
        }

        return '';
    }
}
