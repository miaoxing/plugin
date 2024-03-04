<?php

namespace MiaoxingTest\Plugin\Service;

class DbQueueTest extends QueueTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->queue->setDriver('dbQueue');
    }
}
