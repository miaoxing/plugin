<?php

namespace Miaoxing\Plugin;

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
        $property = lcfirst(str_replace('_', '', ucwords($prefix, '_'))) . 'Table';
        if (isset($this->$property)) {
            $data = $this->$property;
        } else {
            $data = [];
        }

        // 3. Generate id and name
        $prefix .= '_';
        $length = strlen($prefix);
        foreach ($consts as $name => $id) {
            if (stripos($name, $prefix) !== 0) {
                continue;
            }
            if (in_array($name, $this->constExcludes)) {
                continue;
            }
            $data[$id]['id'] = $id;
            $data[$id]['name'] = strtolower(strtr(substr($name, $length), ['_' => '-']));
        }

        self::$consts[$prefix] = $data;

        return $data;
    }

    /**
     * Returns the constant value by specified id and key
     *
     * @param string $prefix
     * @param int $id
     * @param string $key
     * @return mixed
     */
    public function getConstValue($prefix, $id, $key)
    {
        $consts = $this->getConsts($prefix);

        return isset($consts[$id][$key]) ? $consts[$id][$key] : null;
    }

    /**
     * Returns the constant name by id
     *
     * @param string $prefix
     * @param int $id
     * @return mixed
     */
    public function getConstName($prefix, $id)
    {
        return $this->getConstValue($prefix, $id, 'name');
    }

    /**
     * Returns the constant label by id
     *
     * @param string $prefix
     * @param int $id
     * @return string
     */
    public function getConstLabel($prefix, $id)
    {
        return $this->getConstValue($prefix, $id, 'label');
    }

    /**
     * Returns the constant id by name
     *
     * @param string $prefix
     * @param string $name
     * @return int
     */
    public function getConstIdByName($prefix, $name)
    {
        $nameToIds = $this->getConstNameToIds($prefix);

        return isset($nameToIds[$name]) ? $nameToIds[$name] : null;
    }

    /**
     * Returns the name to id map
     *
     * @param string $prefix
     * @return array
     */
    protected function getConstNameToIds($prefix)
    {
        if (!isset(self::$constNameToIds[$prefix])) {
            $consts = $this->getConsts($prefix);
            $nameToIds = array_flip(wei()->coll->column($consts, 'name'));
            self::$constNameToIds[$prefix] = $nameToIds;
        }

        return self::$constNameToIds[$prefix];
    }
}
