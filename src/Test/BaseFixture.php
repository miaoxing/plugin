<?php

namespace Miaoxing\Plugin\Test;

use miaoxing\plugin\BaseService;

/**
 * @property BaseTestCase $testCase
 */
class BaseFixture extends BaseService
{
    /**
     * @return string
     */
    protected function getNow()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * @return string
     */
    protected function getStartTime()
    {
        return date('Y-m-d H:i:s', time() - 100);
    }

    /**
     * @return string
     */
    protected function getEndTime()
    {
        return date('Y-m-d H:i:s', time() + 100);
    }

    /**
     * @param mixed $property
     * @param callable $fn
     * @return mixed
     * @deprecated 影响代码提示,废弃,使用 $this->xxx || $this->xxx = xxx代替
     */
    public function set(&$property, callable $fn)
    {
        return $property ?: $property = $fn();
    }
}
