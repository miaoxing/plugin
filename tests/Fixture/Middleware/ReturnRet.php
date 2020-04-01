<?php

namespace MiaoxingTest\Plugin\Fixture\Middleware;

use Miaoxing\Services\Middleware\BaseMiddleware;
use Wei\RetTrait;

class ReturnRet extends BaseMiddleware
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
