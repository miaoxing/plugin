<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\QueueWorker;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\Job\TestJob;
use MiaoxingTest\Plugin\Fixture\Job\TestModel;
use MiaoxingTest\Plugin\Fixture\Job\TestRelease;
use MiaoxingTest\Plugin\Fixture\Job\TestRestart;
use MiaoxingTest\Plugin\Fixture\Job\TestRetry;
use MiaoxingTest\Plugin\Model\Fixture\DbTrait;
use MiaoxingTest\Plugin\Model\Fixture\TestUser;
use Wei\QueryBuilder;

/**
 * @mixin \QueuePropMixin
 * @mixin \QueueWorkerPropMixin
 * @mixin \DbPropMixin
 * @mixin \TimePropMixin
 * @mixin \CachePropMixin
 * @mixin \ArrayCachePropMixin
 */
abstract class BaseQueueTest extends BaseTestCase
{
    use DbTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queue->clear();
    }

    protected function tearDown(): void
    {
        $this->wei->remove('queueWorker');
        $this->wei->remove('queue');

        parent::tearDown();
    }

    public function testDispatch()
    {
        $this->arrayCache->delete('__prop1');

        TestJob::dispatch();

        $this->queueWorker->runNextJob();

        $this->assertSame('test', $this->arrayCache->get('__prop1'));
    }

    public function testDispatchWithArgs()
    {
        $this->arrayCache->delete('__prop1');

        TestJob::dispatch('foo', 'bar');

        /** @var TestJob $job */
        $job = $this->queueWorker->runNextJob();

        // Payload loaded
        $this->assertSame(['prop1' => 'foo', 'prop2' => 'bar'], $job->getPayload()['data']);

        // Called __invoke
        $this->assertSame('foo', $this->arrayCache->get('__prop1'));

        // Set prop values
        $this->assertSame('foo', $job->getProp1());
        $this->assertSame('bar', $job->getProp2());
    }

    public function testDispatchWithDefaultMethodArgs()
    {
        TestJob::dispatch();

        /** @var TestJob $job */
        $job = $this->queueWorker->runNextJob();

        $this->assertSame(2, $job->getProp2());
    }

    public function testDispatchWithModelArgs()
    {
        $this->initFixtures();

        $model = TestUser::first();

        TestModel::dispatch($model);

        $this->queueWorker->runNextJob();

        $this->assertSame($model->toArray(), $this->arrayCache->get('__queue'));
    }

    public function testDispatchWithModelCollArgs()
    {
        $this->initFixtures();

        $model = TestUser::new()->limit(2)->all();

        TestModel::dispatch($model);

        $this->queueWorker->runNextJob();

        $this->assertCount(2, $this->arrayCache->get('__queue'));
        $this->assertSame($model->toArray(), $this->arrayCache->get('__queue'));
    }

    public function testDispatchOnQueue()
    {
        $this->arrayCache->delete('__prop1');

        TestJob::onQueue('test')->dispatch('prop1', 'prop2');

        $this->queueWorker->runNextJob();
        $this->assertFalse($this->arrayCache->has('__prop1'));

        $this->queueWorker->runNextJob([
            'queueName' => 'test',
        ]);
        $this->assertSame('prop1', $this->arrayCache->get('__prop1'));
    }

    public function testId()
    {
        $job1 = new TestJob();
        $this->assertNull($job1->getJobId(), 'New job dont have id');

        $job1->dispatch();
        $this->assertNotNull($job1->getJobId());

        $job2 = TestJob::dispatch();
        $this->assertNotNull($job2->getJobId());

        $this->assertNotSame($job1->getJobId(), $job2->getJobId());

        $processedJob1 = $this->queueWorker->runNextJob();
        $this->assertSame($job1->getJobId(), $processedJob1->getJobId());

        $processedJob2 = $this->queueWorker->runNextJob();
        $this->assertSame($job2->getJobId(), $processedJob2->getJobId());
    }

    public function testQueueName()
    {
        $job = new TestJob();
        $this->assertNull($job->getQueueName());

        $job->onQueue('test');
        $this->assertSame('test', $job->getQueueName());
    }

    public function testDelay()
    {
        if ('syncQueue' === $this->queue->getDriver()) {
            $this->markTestSkipped('Sync queue does not support delay');
        }

        $job = new TestJob();
        $this->assertSame(0, $job->getDelay());

        $job->delay(10);
        $this->assertSame(10, $job->getDelay());

        $job->dispatch();

        $job = $this->queueWorker->runNextJob();
        $this->assertNull($job);

        $this->time->setTimestamp(time() + 10);
        $job = $this->queueWorker->runNextJob();
        $this->assertFalse($job->isFailed());
        $this->assertNotNull($job);

        $this->time->setTimestamp(null);
    }

    public function testDelete()
    {
        $job = TestJob::dispatch('test');
        $this->assertFalse($job->isDeleted());

        $job = $this->queueWorker->runNextJob();
        $this->assertFalse($job->isFailed());
        $this->assertTrue($job->isDeleted());
    }

    public function testRelease()
    {
        TestRelease::dispatch();

        // Released
        $job = $this->queueWorker->runNextJob();
        $this->assertNotNull($job);
        $this->assertFalse($job->isFailed());
        $this->assertTrue($job->isReleased());
        $this->assertFalse($job->isDeleted());

        // Released
        $job = $this->queueWorker->runNextJob();
        $this->assertNotNull($job);
        $this->assertFalse($job->isFailed());
        $this->assertTrue($job->isReleased());
        $this->assertFalse($job->isDeleted());

        // Processed
        $job = $this->queueWorker->runNextJob();
        $this->assertNotNull($job);
        $this->assertFalse($job->isFailed());
        $this->assertFalse($job->isReleased());
        $this->assertTrue($job->isDeleted());

        // Clear state in `TestRelease`
        $this->arrayCache->clear();
    }

    public function testRetry()
    {
        TestRetry::dispatch();

        $job = $this->queueWorker->runNextJob([
            'maxTries' => 3,
        ]);
        $this->assertFalse($job->isFailed(), 'retry is not failed');
        $this->assertSame(1, $job->attempts());
        $this->assertTrue($job->isReleased(), 'retry will release job');

        $job = $this->queueWorker->runNextJob();
        $this->assertFalse($job->isFailed());
        $this->assertSame(2, $job->attempts());
        $this->assertTrue($job->isReleased());

        $job = $this->queueWorker->runNextJob();
        $this->assertFalse($job->isFailed());
        $this->assertSame(3, $job->attempts());
        $this->assertFalse($job->isReleased());
    }

    public function testRetryDelay()
    {
        if ('syncQueue' === $this->queue->getDriver()) {
            $this->markTestSkipped('Sync queue does not support delay');
        }

        TestRetry::dispatch();

        $job = $this->queueWorker->runNextJob([
            'maxTries' => 3,
            'delay' => 10,
        ]);
        $this->assertTrue($job->isReleased());

        $job = $this->queueWorker->runNextJob();
        $this->assertNull($job, 'Should receive after 10 seconds');

        $this->time->setTimestamp(time() + 10);
        $job = $this->queueWorker->runNextJob();
        $this->assertFalse($job->isFailed());
        $this->assertSame(2, $job->attempts());
        $this->assertTrue($job->isReleased());

        $this->time->setTimestamp(time() + 20);
        $job = $this->queueWorker->runNextJob();
        $this->assertFalse($job->isFailed());
        $this->assertSame(3, $job->attempts());
        $this->assertFalse($job->isReleased());

        $this->time->setTimestamp();
    }

    public function testJobWillBeDeletedAfterFail()
    {
        TestRetry::dispatch();

        $job = $this->queueWorker->runNextJob();

        $this->assertTrue($job->isFailed());
        $this->assertTrue($job->isDeleted());
    }

    public function testLogFailJob()
    {
        $queueName = 'fail';

        TestRetry::onQueue($queueName)->dispatch();

        $job = $this->queueWorker->runNextJob(['queueName' => $queueName]);
        $this->assertTrue($job->isFailed());

        $job = QueryBuilder::table('queue_failed_jobs')->desc('id')->fetch();

        $this->assertSame($this->queue->getDriver(), $job['driver']);
        $this->assertSame($queueName, $job['queue']);

        $payload = json_decode($job['payload'], true);
        $this->assertSame(TestRetry::class, $payload['job']);

        $this->assertStringStartsWith('Exception: test', $job['exception']);
    }

    public function testQueuePriority()
    {
        TestJob::onQueue('test1')->dispatch('test1');
        TestJob::onQueue('test2')->dispatch('test2');

        /** @var TestJob $job */
        $job = $this->queueWorker->runNextJob([
            'queueName' => 'test2,test1',
        ]);
        $this->assertSame('test2', $job->getProp1());

        /** @var TestJob $job */
        $job = $this->queueWorker->runNextJob();
        $this->assertSame('test1', $job->getProp1());
    }

    public function testCallSleepWhenNoJob()
    {
        $worker = $this->getServiceMock(QueueWorker::class, ['sleep', 'jobLimitExceeded']);

        $worker->expects($this->once())
            ->method('sleep');

        // Only run once
        $worker->expects($this->once())
            ->method('jobLimitExceeded')
            ->willReturn(true);

        $this->queueWorker->run();
    }

    public function testWorkerJobLimit()
    {
        TestJob::dispatch();
        $job = TestJob::dispatch();

        $this->queueWorker->run([
            // Only run once
            'jobLimit' => 1,
        ]);

        $job2 = $this->queueWorker->runNextJob();
        $this->assertSame($job->getJobId(), $job2->getJobId());
    }

    public function testWorkerMemoryLimit()
    {
        TestJob::dispatch();
        $job = TestJob::dispatch();

        $this->queueWorker->run([
            // Only run once
            'memoryLimit' => 1,
        ]);

        $job2 = $this->queueWorker->runNextJob();
        $this->assertSame($job->getJobId(), $job2->getJobId());
    }

    public function testWorkerTimeLimit()
    {
        TestJob::dispatch();
        $job = TestJob::dispatch();

        $this->queueWorker->run([
            // Only run once
            'timeLimit' => 0,
        ]);

        $job2 = $this->queueWorker->runNextJob();
        $this->assertSame($job->getJobId(), $job2->getJobId());
    }

    public function testRestart()
    {
        TestRestart::dispatch();
        $job = TestRestart::dispatch();

        $this->queueWorker->run();

        $job2 = $this->queueWorker->runNextJob();
        $this->assertSame($job->getJobId(), $job2->getJobId());

        // @internal Reset internal cache for next queue driver
        $this->cache->delete('wei:queue:restart');
    }
}
