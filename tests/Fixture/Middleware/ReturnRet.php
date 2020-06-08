<?php

namespace MiaoxingTest\Plugin\Fixture\Middleware;

use Wei\Base;
use Wei\RetTrait;

class ReturnRet extends Base
{
    use RetTrait;

    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        return $this->suc('returnRetInMiddleware');
    }
}
