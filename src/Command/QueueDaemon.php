<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \QueueWorkerMixin
 */
class QueueDaemon extends BaseCommand
{
    protected function handle()
    {
        $this->queueWorker->work([
            'daemon' => true,
            'queueName' => $this->getArgument('name'),
        ]);
    }

    protected function configure()
    {
        $this->setDescription('Run the queue worker')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of queue');
    }
}
