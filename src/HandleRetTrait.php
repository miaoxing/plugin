<?php

namespace Miaoxing\Plugin;

use Wei\Ret;

/**
 * @deprecated Use $ret->assert instead
 */
trait HandleRetTrait
{
    /**
     * Throw exception if ret is error
     *
     * @param Ret $ret
     * @throws RetException
     */
    protected function tie(Ret $ret)
    {
        if ($ret->isErr()) {
            throw new RetException($ret);
        }
    }
}
