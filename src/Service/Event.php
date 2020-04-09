<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Services\Service\StaticTrait;

class Event extends \Wei\Event
{
    use StaticTrait;

    /**
     * @param string $name
     * @param array $args
     * @param bool $halt
     * @return array|mixed
     * @api
     */
    protected function trig($name, $args = array(), $halt = false)
    {
        return $this->trigger(...func_get_args());
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     * @api
     */
    protected function trigUtil($name, $args = array())
    {
        return $this->until(...func_get_args());
    }
}
