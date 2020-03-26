<?php

namespace Miaoxing\Plugin\Db;

abstract class BaseDriver
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
