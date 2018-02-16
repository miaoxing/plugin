<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;

/**
 * 时间日期
 */
class Time extends BaseService
{
    protected $now;

    /**
     * @return string
     */
    public function __invoke()
    {
        return date('Y-m-d H:i:s', $this->getNow());
    }

    public function now()
    {
        return $this->__invoke();
    }

    public function today()
    {
        return date('Y-m-d', $this->getNow());
    }

    protected function getNow()
    {
        return $this->now ?: time();
    }
}
