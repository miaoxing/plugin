<?php

namespace MiaoxingTest\Plugin\Fixture\Job;

use Miaoxing\Plugin\Queue\BaseJob;

class TestRetryJob extends BaseJob
{
    public function __invoke($data)
    {
        if ($this->attempts() < 2) {
            throw new \Exception('test');
        } else {
            $this->delete();
        }
    }
}
