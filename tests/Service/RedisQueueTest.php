<?php

namespace MiaoxingTest\Plugin\Service;

class RedisQueueTest extends BaseQueueTest
{
    protected function setUp(): void
    {
        $this->queue->setDriver('redisQueue');

        parent::setUp();
    }

    protected function tearDown(): void
    {
        // Remove the queue to create group again
        $this->wei->remove('redisQueue');
        parent::tearDown();
    }
}
