<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \EventPropMixin
 * @mixin \SchedulePropMixin
 */
class ScheduleRun extends BaseCommand
{
    protected function handle()
    {
        $this->schedule->setOutput($this->output);

        $name = $this->getArgument('name');
        if ($name) {
            $this->schedule->runByName($name);
        } else {
            $this->schedule->run();
        }
    }

    protected function configure()
    {
        $this->setDescription('Run the scheduled tasks')
            ->addArgument('name', InputArgument::OPTIONAL, 'Run the specified task by name');
    }
}
