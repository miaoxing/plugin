<?php

namespace MiaoxingTest\Plugin\Service;

/**
 * @mixin \SyncQueueMixin
 */
class SyncQueueTest extends QueueTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->queue->setDriver('syncQueue');

        $this->syncQueue->setManual(true);
    }
}
