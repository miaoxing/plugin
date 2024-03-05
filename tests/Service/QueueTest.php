<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\QueueWorker;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\FailingSyncQueueTestHandler;
use MiaoxingTest\Plugin\Fixture\Job\TestJob;
use MiaoxingTest\Plugin\Fixture\Job\TestRetryJob;
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
        $this->queueWorker->setSleep(0);
    }

    public function tearDown(): void
    {
        $this->wei->remove('queueWorker');

        parent::tearDown();
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

        $this->queueWorker->work();
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

        try {
            FailingSyncQueueTestHandler::dispatch(['foo' => 'bar']);
            $this->queueWorker->work();
        } catch (\Exception $e) {
            $this->assertTrue($_SERVER['__sync.failed']);
        }
    }

    public function testTries()
    {
        // 为了能直接获取到失败的任务
        $this->dbQueue->setOption('expire', 0);

        // TODO 主动插入
        $job = TestRetryJob::dispatch('test');
        $this->queue->pushJob($job);

        $result = $this->queueWorker->work([
            'tries' => 2,
        ]);
        $this->assertTrue($result['failed']);
        $this->assertSame(0, $result['job']->attempts());

        $result = $this->queueWorker->work();
        $this->assertTrue($result['failed']);
        $this->assertSame(1, $result['job']->attempts());

        $result = $this->queueWorker->work();
        $this->assertFalse($result['failed']);
        $this->assertSame(2, $result['job']->attempts());
    }
}
