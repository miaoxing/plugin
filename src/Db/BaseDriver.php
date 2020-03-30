<?php

namespace Miaoxing\Plugin\Db;

use Wei\Base;
use Wei\Db;

/**
 * @property Db db
 */
abstract class BaseDriver extends Base
{
    protected function wrap($column)
    {
        return $column === '*' ? '*' : $this->wrapper . $column . $this->wrapper;
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
