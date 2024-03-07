<?php

namespace MiaoxingTest\Plugin\Service;

/**
 * @mixin \DbQueuePropMixin
 */
class DbQueueTest extends BaseQueueTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->queue->setDriver('dbQueue');

        $this->dbQueue->setRetryAfter(0);
    }
}
