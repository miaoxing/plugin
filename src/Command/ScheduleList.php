<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Helper\Table;

/**
 * @mixin \SchedulePropMixin
 */
class ScheduleList extends BaseCommand
{
    protected function handle()
    {
        $tasks = $this->schedule->getTasks();

        $table = new Table($this->output);
        $table->setHeaders(['Name', 'Cron', 'Next Due']);

        foreach ($tasks as $task) {
            $table->addRow([
                $task->getName(),
                $task->getCron(),
                $task->getNextRunDate()->format('Y-m-d H:i:s'),
            ]);
        }

        $table->render();
    }

    protected function configure()
    {
        $this->setDescription('List the scheduled tasks');
    }
}
