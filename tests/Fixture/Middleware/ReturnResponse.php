<?php

namespace MiaoxingTest\Plugin\Fixture\Middleware;

use Wei\Base;

/**
 * @mixin \ResponseMixin
 */
class ReturnResponse extends Base
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        return $this->response->setContent('returnResponseInMiddleware');
    }
}
