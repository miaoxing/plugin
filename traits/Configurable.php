<?php

namespace miaoxing\plugin\traits;

trait Configurable
{
    /**
     * The configuration of the plugin
     *
     * @var array
     */
    protected $configs = [];

    /**
     * Get configuration
     *
     * @param string $name
     * @return mixed
     */
    public function getConfig($name)
    {
        return isset($this->configs[$name]) ? $this->configs[$name] : null;
    }

    /**
     * Set configuration
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setConfig($name, $value)
    {
        $this->configs[$name] = $value;

        return $this;
    }
}
