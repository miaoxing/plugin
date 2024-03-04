<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\FailingSyncQueueTestHandler;
use MiaoxingTest\Plugin\Fixture\Job\TestJob;
use Wei\Event;

/**
 * @mixin \QueuePropMixin
 * @mixin \QueueWorkerPropMixin
 */
class QueueTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->queue->clear();
        $this->queueWorker->setOption('sleep', 0);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->wei->remove('queueWorker');
    }

    public function testDispatch()
    {
        unset($_SERVER['__queue']);

        TestJob::dispatch();

        $this->queueWorker->work();

        $this->assertSame([], $_SERVER['__queue']);
    }

    public function testDispatchWithArgs()
    {
        unset($_SERVER['__queue']);

        TestJob::dispatch('foo', 'bar');

        $this->queueWorker->work();

        $this->assertSame(['foo', 'bar'], $_SERVER['__queue']);
    }

    public function testDispatchIf()
    {
        unset($_SERVER['__queue']);

        TestJob::dispatchIf(true, 'foo', 'bar');

        $this->queueWorker->work();

        $this->assertSame(['foo', 'bar'], $_SERVER['__queue']);
    }

    public function testDispatchIfWithFalse()
    {
        unset($_SERVER['__queue']);

        TestJob::dispatchIf(false);

        $this->queueWorker->work();

        $this->assertArrayNotHasKey('__queue', $_SERVER);
    }


    public function testDispatchOnQueue()
    {
        unset($_SERVER['__queue']);

        TestJob::dispatch('test')->onQueue('test');

        $this->queueWorker->work([
            'queueName' => 'default',
        ]);
        $this->assertArrayNotHasKey('__queue', $_SERVER);

        $this->queueWorker->work([
            'queueName' => 'test',
        ]);
        $this->assertSame(['test'], $_SERVER['__queue']);
    }

    /**
     * @link https://github.com/laravel/framework/blob/5.1/tests/Queue/QueueSyncQueueTest.php
     */
    public function testFailedJobGetsHandledWhenAnExceptionIsThrown()
    {
        unset($_SERVER['__sync.failed']);

        $event = $this->getServiceMock(Event::class, ['trigger']);
        $event->expects($this->once())
            ->method('trigger')
            ->with('queueFailed');

        FailingSyncQueueTestHandler::dispatch(['foo' => 'bar']);

        try {
            $this->queueWorker->work();
        } catch (\Exception $e) {
            $this->assertTrue($_SERVER['__sync.failed']);
        }
    }
}
