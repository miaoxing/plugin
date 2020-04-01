<?php

namespace MiaoxingTest\Plugin\Fixture\Middleware;

use Miaoxing\Services\Middleware\BaseMiddleware;

class ReturnResponse extends BaseMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        return $this->response->setContent('returnResponseInMiddleware');
    }
}
