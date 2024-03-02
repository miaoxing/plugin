<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\FailingSyncQueueTestHandler;
use Wei\Event;

/**
 * @link https://github.com/laravel/framework/blob/5.1/tests/Queue/QueueSyncQueueTest.php
 * @mixin \SyncQueuePropMixin
 */
class SyncQueueTest extends BaseTestCase
{
    public function testFailedJobGetsHandledWhenAnExceptionIsThrown()
    {
        unset($_SERVER['__sync.failed']);
        $sync = $this->syncQueue;

        $event = $this->getServiceMock(Event::class, ['trigger']);
        $event->expects($this->once())
            ->method('trigger')
            ->with('queueFailed');

        try {
            $sync->push(FailingSyncQueueTestHandler::class, ['foo' => 'bar']);
        } catch (\Exception $e) {
            $this->assertTrue($_SERVER['__sync.failed']);
        }
    }
}
