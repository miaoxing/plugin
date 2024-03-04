<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\Queue\BaseJob;

class FailingSyncQueueTestHandler extends BaseJob
{
    public function __invoke($data)
    {
        throw new \Exception();
    }

    public function failed()
    {
        $_SERVER['__sync.failed'] = true;
    }
}
