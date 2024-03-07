<?php

namespace MiaoxingTest\Plugin\Service;

/**
 * @mixin \SyncQueueMixin
 */
class SyncQueueTest extends BaseQueueTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->queue->setDriver('syncQueue');

        $this->syncQueue->setManual(true);
    }
}
