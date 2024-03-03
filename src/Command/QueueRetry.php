<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \QueueWorkerMixin
 */
class QueueRetry extends BaseCommand
{
    protected function handle()
    {
        $this->queueWorker->retry($this->getArgument('id'));
    }

    protected function configure()
    {
        $this->setDescription('Retry failed job')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the job to retry');
    }
}
