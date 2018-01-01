<?php

namespace Miaoxing\Plugin;

trait Constant
{
    /**
     * @var array
     */
    protected static $constants = [];

    /**
     * @var array
     */
    protected static $constantNameToIds = [];

    /**
     * @var array
     */
    protected $constantExcludes = [
        'STATE_DIRTY',
        'STATE_CLEAN',
    ];

    /**
     * Get constants table by prefix
     *
     * @param string $prefix
     * @return array
     */
    public function getConstants($prefix)
    {
        if (isset(self::$constants[$prefix])) {
            return self::$constants[$prefix];
        }

        // 1. Get all class constants
        $class = new \ReflectionClass($this);
        $constants = $class->getConstants();

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
        foreach ($constants as $name => $id) {
            if (stripos($name, $prefix) !== 0) {
                continue;
            }
            if (in_array($name, $this->constantExcludes)) {
                continue;
            }
            $data[$id]['id'] = $id;
            $data[$id]['name'] = strtolower(strtr(substr($name, $length), ['_' => '-']));
        }

        self::$constants[$prefix] = $data;

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
    public function getConstantValue($prefix, $id, $key)
    {
        $constants = $this->getConstants($prefix);

        return isset($constants[$id][$key]) ? $constants[$id][$key] : null;
    }

    /**
     * Returns the constant name by id
     *
     * @param string $prefix
     * @param int $id
     * @return mixed
     */
    public function getConstantNameById($prefix, $id)
    {
        return $this->getConstantValue($prefix, $id, 'name');
    }

    /**
     * Returns the constant id by name
     *
     * @param string $prefix
     * @param string $name
     * @return int
     */
    public function getConstantIdByName($prefix, $name)
    {
        $nameToIds = $this->getConstantNameToIds($prefix);

        return isset($nameToIds[$name]) ? $nameToIds[$name] : null;
    }

    /**
     * Returns the constant label by id
     *
     * @param string $prefix
     * @param int $id
     * @return string
     */
    public function getConstantLabel($prefix, $id)
    {
        return $this->getConstantValue($prefix, $id, 'label');
    }

    /**
     * Returns the name to id map
     *
     * @param string $prefix
     * @return array
     */
    protected function getConstantNameToIds($prefix)
    {
        if (!isset(self::$constantNameToIds[$prefix])) {
            $constants = $this->getConstants($prefix);
            $nameToIds = array_flip(wei()->coll->column($constants, 'name'));
            self::$constantNameToIds[$prefix] = $nameToIds;
        }

        return self::$constantNameToIds[$prefix];
    }
}
