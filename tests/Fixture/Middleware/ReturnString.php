<?php

namespace MiaoxingTest\Plugin\Fixture\Middleware;

use Miaoxing\Services\Middleware\BaseMiddleware;

class ReturnString extends BaseMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        return 'returnStringInMiddleware';
    }
}
