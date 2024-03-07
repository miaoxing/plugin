<?php

namespace MiaoxingTest\Plugin\Fixture\Job;

use Miaoxing\Plugin\Queue\BaseJob;

class TestRetry extends BaseJob
{
    protected const MAX_ATTEMPTS = 3;

    public function __invoke(): void
    {
        if ($this->attempts() < static::MAX_ATTEMPTS) {
            throw new \Exception('test');
        }
    }
}
