<?php

namespace Miaoxing\Plugin\Db;

use Wei\Base;
use Wei\Db;

/**
 * @property Db db
 */
abstract class BaseDriver extends Base
{
    protected function wrap($value)
    {
        return $this->wrapper . $value . $this->wrapper;
    }

    protected function getRawValue($expression)
    {
        return $expression->scalar;
    }

    protected function isRaw($expression)
    {
        return $expression instanceof \stdClass && isset($expression->scalar);
    }
}
