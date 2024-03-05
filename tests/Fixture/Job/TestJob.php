<?php

namespace MiaoxingTest\Plugin\Fixture\Job;

use Miaoxing\Plugin\Queue\BaseJob;

class TestJob extends BaseJob
{
    public function __invoke($data)
    {
        $_SERVER['__queue'] = $data;
    }
}
