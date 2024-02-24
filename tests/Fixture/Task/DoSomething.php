<?php

namespace MiaoxingTest\Plugin\Fixture\Task;

use Miaoxing\Plugin\Schedule\Task;

class DoSomething extends Task
{
    public function run()
    {
        return 'do something';
    }
}
