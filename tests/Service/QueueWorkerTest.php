<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;
use Miaoxing\Plugin\Service\Queue;
use Miaoxing\Plugin\Service\QueueWorker;
use Miaoxing\Plugin\Test\BaseTestCase;
use Wei\Db;

/**
 * @link https://github.com/laravel/framework/blob/5.1/tests/Queue/QueueWorkerTest.php
 * @mixin \QueueWorkerMixin
 */
class QueueWorkerTest extends BaseTestCase
{
    public function testJobIsPoppedOffQueueAndProcessed()
    {
        $queue = $this->getServiceMock(Queue::class, ['pop']);

        $worker = $this->getServiceMock(QueueWorker::class, ['process']);

        $job = $this->createMock(BaseJob::class);

        $queue->expects($this->once())
            ->method('pop')
            ->with('queue')
            ->willReturn($job);

        $worker->expects($this->once())
            ->method('process')
            ->with($job, 0, 0);

        $worker->pop('queue', 0, 0);
    }

    public function testJobIsPoppedOffFirstQueueInListAndProcessed()
    {
        $queue = $this->getServiceMock(Queue::class, ['pop']);

        $worker = $this->getServiceMock(QueueWorker::class, ['process']);

        $job = $this->createMock(BaseJob::class);

        $queue->expects($this->at(0))
            ->method('pop')
            ->with('queue1')
            ->willReturn(null);

        $queue->expects($this->at(1))
            ->method('pop')
            ->with('queue2')
            ->willReturn($job);

        $worker->expects($this->once())
            ->method('process')
            ->with($job, 0, 0);

        $worker->pop('queue1,queue2', 0, 0);
    }

    public function testWorkerSleepsIfNoJobIsPresentAndSleepIsEnabled()
    {
        $queue = $this->getServiceMock(Queue::class, ['pop']);

        $worker = $this->getServiceMock(QueueWorker::class, ['process', 'sleep']);

        $queue->expects($this->once())
            ->method('pop')
            ->with('queue')
            ->willReturn(null);

        $worker->expects($this->never())
            ->method('process');

        $worker->expects($this->once())
            ->method('sleep')
            ->with(2);

        $worker->pop('queue', 0, 2);
    }

    public function testWorkerLogsJobToFailedQueueIfMaxTriesHasBeenExceeded()
    {
        $queue = $this->getServiceMock(Queue::class, ['pop']);

        $db = $this->getServiceMock(Db::class, ['insert']);

        $worker = $this->getServiceMock(QueueWorker::class, ['sleep', 'getTime']);

        $time = time();
        $worker->expects($this->once())
            ->method('getTime')
            ->willReturn($time);

        $job = $this->createMock(BaseJob::class);

        $job->expects($this->once())
            ->method('getQueueName')
            ->willReturn('default');

        $job->expects($this->once())
            ->method('attempts')
            ->willReturn(10);

        $job->expects($this->exactly(3))
            ->method('getPayload')
            ->willReturn(['key' => 'value']);

        $job->expects($this->once())
            ->method('delete');

        $job->expects($this->once())
            ->method('failed');

        $db->expects($this->once())
            ->method('insert')
            ->with('queue_failed_jobs', [
                'queue' => 'default',
                'payload' => '{"key":"value"}',
                'created_at' => date('Y-m-d H:i:s', $time),
            ]);

        $worker->process($job, 3);
    }

    public function testJobIsReleasedWhenExceptionIsThrown()
    {
        $this->expectException(\RuntimeException::class);

        $job = $this->createMock(BaseJob::class);

        $job->expects($this->once())
            ->method('fire')
            ->willReturnCallback(static function () {
                throw new \RuntimeException();
            });

        $job->expects($this->once())
            ->method('isDeleted')
            ->willReturn(false);

        $job->expects($this->once())
            ->method('release')
            ->with(5);

        $this->queueWorker->process($job, 0, 5);
    }

    public function testJobIsNotReleasedWhenExceptionIsThrownButJobIsDeleted()
    {
        $this->expectException(\RuntimeException::class);

        $job = $this->createMock(BaseJob::class);

        $job->expects($this->once())
            ->method('fire')
            ->willReturnCallback(static function () {
                throw new \RuntimeException();
            });

        $job->expects($this->once())
            ->method('isDeleted')
            ->willReturn(true);

        $job->expects($this->never())
            ->method('release');

        $this->queueWorker->process($job, 0, 5);
    }
}
