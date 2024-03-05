<?php

namespace MiaoxingTest\Plugin\Fixture\Job;

use Miaoxing\Plugin\Queue\BaseJob;

class TestRelease extends BaseJob
{
    public function __invoke($data)
    {
        if (!isset($_SERVER['__release'])) {
            $_SERVER['__release'] = 0;
        }

        // Simulating the first two external calls failed
        if ($_SERVER['__release']++ < 2) {
            $this->release();
        }
    }
}
