<?php

namespace Miaoxing\Plugin;

/**
 * @property array $configs
 */
trait ConfigTrait
{
    public function hasConfig($name)
    {
        return isset($this->configs[$name]);
    }

    public function __set($name, $value)
    {
        if (!$this->hasConfig($name)) {
            return;
        }

        $this->setOption($name, $value);
        // 存储到数据库中.
    }
}
