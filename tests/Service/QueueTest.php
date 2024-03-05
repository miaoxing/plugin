<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\FailingSyncQueueTestHandler;
use MiaoxingTest\Plugin\Fixture\Job\TestJob;
use MiaoxingTest\Plugin\Fixture\Job\TestRelease;
use MiaoxingTest\Plugin\Fixture\Job\TestRetryJob;
use Wei\Event;
use Wei\QueryBuilder;

/**
 * @mixin \QueuePropMixin
 * @mixin \QueueWorkerPropMixin
 * @mixin \DbMixin
 */
class QueueTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

//        $this->queue = $this->redisQueue;

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

        $job = TestJob::dispatch('foo', 'bar');
        $this->queue->pushJob($job);

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

    public function testId()
    {
        $job = TestJob::enqueue();
        $this->assertNotEmpty($job->getId());

        $job2 = TestJob::enqueue();
        $this->assertNotEmpty($job2->getId());

        $this->assertNotEmpty($job->getId(), $job2->getId());
    }

    public function testQueueName()
    {
        $job = TestRetryJob::dispatch('test');
        $this->assertNull($job->getQueueName());

        $job->onQueue('test');
        $this->assertSame('test', $job->getQueueName());
    }

    public function testDelay()
    {
        $job = TestJob::dispatch('test');
        $this->assertSame(0, $job->getDelay());

        $job->delay(10);
        $this->assertSame(10, $job->getDelay());

        $this->queue->pushJob($job);

        $result = $this->queueWorker->work();
        $this->assertFalse($result['failed']);
        $this->assertNull($result['job']);

        $this->queue->setTime(time() + 10);
        $result = $this->queueWorker->work();
        $this->assertFalse($result['failed']);
        $this->assertNotNull($result['job']);
    }

    public function testRelease()
    {
        // TODO release后可以再次获取到
        $this->dbQueue->setOption('expire', 0);

        TestRelease::enqueue('release');

        // Released
        $result = $this->queueWorker->work();
        $this->assertFalse($result['failed']);
        $this->assertNotNull($result['job']);
        $this->assertTrue($result['job']->isReleased());
        $this->assertFalse($result['job']->isDeleted());

        // Released
        $result = $this->queueWorker->work();
        $this->assertFalse($result['failed']);
        $this->assertNotNull($result['job']);
        $this->assertTrue($result['job']->isReleased());
        $this->assertFalse($result['job']->isDeleted());

        // Processed
        $result = $this->queueWorker->work();
        $this->assertFalse($result['failed']);
        $this->assertNotNull($result['job']);
        $this->assertFalse($result['job']->isReleased());
        $this->assertTrue($result['job']->isDeleted());
    }

    public function testRetrySuccess()
    {
        // TODO 失败后可以再次获取到
        $this->queue->setOption('expire', 0);

        TestRetryJob::enqueue('test');

        $result = $this->queueWorker->work([
            'tries' => 3,
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

    public function testJobWillBeDeletedAfterFail()
    {
        TestRetryJob::enqueue('test');

        $result = $this->queueWorker->work();

        $this->assertTrue($result['failed']);
        $this->assertTrue($result['job']->isDeleted());
    }

    public function testLogFailJob()
    {
        $queueName = 'fail';

        TestRetryJob::onQueue($queueName)->enqueue('test');

        $result = $this->queueWorker->work(['queueName' => $queueName]);
        $this->assertTrue($result['failed']);

        $job = QueryBuilder::table('queue_failed_jobs')->desc('id')->fetch();

        $this->assertSame($this->queue->getDriver(), $job['driver']);
        $this->assertSame($queueName, $job['queue']);

        $payload = json_decode($job['payload'], true);
        $this->assertSame(TestRetryJob::class, $payload['job']);

        $this->assertStringStartsWith('Exception: test', $job['exception']);
    }
}
