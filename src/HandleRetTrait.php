<?php

namespace Miaoxing\Plugin;

trait HandleRetTrait
{
    /**
     * Throw exception if ret is error
     *
     * @param array|Ret $ret
     * @throws RetException
     */
    protected function tie($ret)
    {
        if (isset($ret['code']) && $ret['code'] !== 1) {
            throw new RetException($ret);
        }
    }
}
