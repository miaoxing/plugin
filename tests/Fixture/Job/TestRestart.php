<?php

namespace MiaoxingTest\Plugin\Fixture\Job;

use Miaoxing\Plugin\Queue\BaseJob;

/**
 * @mixin \QueueWorkerMixin
 */
class TestRestart extends BaseJob
{
    public function __invoke(): void
    {
        $this->queueWorker->restart();
    }
}
