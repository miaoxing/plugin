<?php

namespace MiaoxingTest\Plugin\Fixture\Middleware;

use Wei\Base;

class ReturnString extends Base
{
    public function __invoke($next)
    {
        return 'returnStringInMiddleware';
    }
}
