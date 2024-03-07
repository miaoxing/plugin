<?php

namespace MiaoxingTest\Plugin\Fixture\Job;

use Miaoxing\Plugin\Queue\BaseJob;

class TestRetry extends BaseJob
{
    public function __invoke(): void
    {
        if ($this->attempts() < 3) {
            throw new \Exception('test');
        }
    }
}
