<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\Queue\BaseJob;

class RedisQueueJob extends BaseJob
{
    public function __invoke($data)
    {
    }
}
