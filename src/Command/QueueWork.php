<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \QueueWorkerMixin
 */
class QueueWork extends BaseCommand
{
    protected function handle()
    {
        $this->queueWorker->work([
            'queueName' => $this->getArgument('name'),
        ]);
    }

    protected function configure()
    {
        $this->setDescription('Run the queue worker')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of queue');
    }
}
