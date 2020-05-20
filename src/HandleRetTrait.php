<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Service\Ret;

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
        if (isset($ret['code']) && 1 !== $ret['code']) {
            throw new RetException($ret);
        }
    }
}
