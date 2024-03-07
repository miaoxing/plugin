<?php

namespace Miaoxing\Plugin\Command;

/**
 * @mixin \QueueWorkerPropMixin
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
