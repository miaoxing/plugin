<?php

namespace Miaoxing\Plugin\Command;

/**
 * @mixin \QueueWorkerMixin
 */
class QueueRestart extends BaseCommand
{
    protected function handle()
    {
        $this->queueWorker->restart();
    }

    protected function configure()
    {
        $this->setDescription('Restart queue worker daemons');
    }
}
