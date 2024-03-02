<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\Job;
use Miaoxing\Plugin\Service\BaseJob;

class FailingSyncQueueTestHandler extends Job
{
    public function __invoke(BaseJob $job, $data)
    {
        throw new \Exception();
    }

    public function failed()
    {
        $_SERVER['__sync.failed'] = true;
    }
}
