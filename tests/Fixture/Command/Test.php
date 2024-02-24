<?php

namespace MiaoxingTest\Plugin\Fixture\Command;

use Miaoxing\Plugin\Command\BaseCommand;

class Test extends BaseCommand
{
    protected function handle()
    {
        $this->output->write('test');
    }
}
