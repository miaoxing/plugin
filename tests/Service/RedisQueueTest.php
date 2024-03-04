<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;
use Miaoxing\Plugin\Service\RedisQueue;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\RedisQueueJob;
use Wei\Redis;

/**
 * @link https://github.com/laravel/framework/blob/5.1/tests/Queue/QueueRedisQueueTest.php
 */
class RedisQueueTest extends BaseTestCase
{
    public function testPushProperlyPushesJobOntoRedis()
    {
        $phpRedis = $this->createMock(\Redis::class);

        $redis = $this->getServiceMock(Redis::class, ['__invoke']);

        $queue = $this->getServiceMock(RedisQueue::class, ['getRandomId']);

        $queue->expects($this->once())
            ->method('getRandomId')
            ->willReturn('foo');

        $redis->expects($this->once())
            ->method('__invoke')
            ->willReturn($phpRedis);

        $phpRedis->expects($this->once())
            ->method('rpush')
            ->with('queues:default', ['job' => 'foo', 'data' => ['data'], 'id' => 'foo', 'attempts' => 1]);

        $id = $queue->push('foo', ['data']);
        $this->assertEquals('foo', $id);
    }

    public function testDelayedPushProperlyPushesJobOntoRedis()
    {
        $phpRedis = $this->createMock(\Redis::class);

        $redis = $this->getServiceMock(Redis::class, ['__invoke']);

        $redis->expects($this->once())
            ->method('__invoke')
            ->willReturn($phpRedis);

        $queue = $this->getServiceMock(RedisQueue::class, ['getSeconds', 'getTime', 'getRandomId']);

        $queue->expects($this->once())
            ->method('getRandomId')
            ->willReturn('foo');

        $queue->expects($this->once())
            ->method('getSeconds')
            ->with(1)
            ->willReturn(1);

        $queue->expects($this->once())
            ->method('getTime')
            ->willReturn(1);

        $phpRedis->expects($this->once())
            ->method('zadd')
            ->with(
                'queues:default:delayed',
                2,
                ['job' => 'foo', 'data' => ['data'], 'id' => 'foo', 'attempts' => 1]
            );

        $id = $queue->later(1, 'foo', ['data']);
        $this->assertEquals('foo', $id);
    }

    public function testDelayedPushWithDateTimeProperlyPushesJobOntoRedis()
    {
        $date = new \DateTime();

        $phpRedis = $this->createMock(\Redis::class);

        $redis = $this->getServiceMock(Redis::class, ['__invoke']);

        $redis->expects($this->once())
            ->method('__invoke')
            ->willReturn($phpRedis);

        $queue = $this->getServiceMock(RedisQueue::class, ['getSeconds', 'getTime', 'getRandomId']);

        $queue->expects($this->once())
            ->method('getRandomId')
            ->willReturn('foo');

        $queue->expects($this->once())
            ->method('getSeconds')
            ->with($date)
            ->willReturn(1);

        $queue->expects($this->once())
            ->method('getTime')
            ->willReturn(1);

        $phpRedis->expects($this->once())
            ->method('zadd')
            ->with(
                'queues:default:delayed',
                2,
                ['job' => 'foo', 'data' => ['data'], 'id' => 'foo', 'attempts' => 1]
            );

        $id = $queue->later($date, 'foo', ['data']);
        $this->assertEquals('foo', $id);
    }

    public function testPopProperlyPopsJobOffOfRedis()
    {
        $phpRedis = $this->createMock(\Redis::class);

        $redis = $this->getServiceMock(Redis::class, ['__invoke']);

        $redis->expects($this->any())
            ->method('__invoke')
            ->willReturn($phpRedis);

        $queue = $this->getServiceMock(RedisQueue::class, ['getTime', 'migrateAllExpiredJobs']);

        $queue->expects($this->once())
            ->method('getTime')
            ->willReturn(1);

        $queue->expects($this->once())
            ->method('migrateAllExpiredJobs')
            ->with('queues:default');

        $phpRedis->expects($this->once())
            ->method('lpop')
            ->with('queues:default')
            ->willReturn([
                'job' => RedisQueueJob::class,
                'data' => [],
            ]);

        $phpRedis->expects($this->once())
            ->method('zadd')
            ->with('queues:default:reserved', 61, [
                'job' => RedisQueueJob::class,
                'data' => [],
            ]);

        $result = $queue->pop();
        $this->assertInstanceOf(BaseJob::class, $result);
    }

    public function testReleaseMethod()
    {
        $phpRedis = $this->createMock(\Redis::class);

        $redis = $this->getServiceMock(Redis::class, ['__invoke']);

        $redis->expects($this->any())
            ->method('__invoke')
            ->willReturn($phpRedis);

        $queue = $this->getServiceMock(RedisQueue::class, ['getTime']);

        $queue->expects($this->once())
            ->method('getTime')
            ->willReturn(1);

        $phpRedis->expects($this->once())
            ->method('zadd')
            ->with('queues:default:delayed', 2, ['attempts' => 2]);

        $queue->release(['attempts' => 1], 1);
    }

    public function testMigrateExpiredJobs()
    {
        $phpRedis = $this->createMock(\Redis::class);

        $redis = $this->getServiceMock(Redis::class, ['__invoke']);

        $redis->expects($this->any())
            ->method('__invoke')
            ->willReturn($phpRedis);

        $queue = $this->getServiceMock(RedisQueue::class, ['getTime']);

        $queue->expects($this->once())
            ->method('getTime')
            ->willReturn(1);

        $phpRedis->expects($this->once())
            ->method('zrangebyscore')
            ->with('from', '-inf', 1)
            ->willReturn(['foo', 'bar']);

        $phpRedis->expects($this->once())
            ->method('zremrangebyscore')
            ->with('from', '-inf', 1);

        $phpRedis->expects($this->once())
            ->method('rpush')
            ->with('to', 'foo', 'bar');

        $queue->migrateExpiredJobs('from', 'to');
    }

    public function testNotExpireJobsWhenExpireNull()
    {
        $phpRedis = $this->createMock(\Redis::class);

        $redis = $this->getServiceMock(Redis::class, ['__invoke']);

        $redis->expects($this->any())
            ->method('__invoke')
            ->willReturn($phpRedis);

        $queue = $this->getServiceMock(RedisQueue::class, ['getTime', 'migrateAllExpiredJobs']);

        $queue->expects($this->once())
            ->method('getTime')
            ->willReturn(1);

        $queue->expects($this->never())
            ->method('migrateAllExpiredJobs');

        $phpRedis->expects($this->once())
            ->method('lpop')
            ->with('queues:default')
            ->willReturn(['job' => RedisQueueJob::class, 'data' => []]);

        $phpRedis->expects($this->once())
            ->method('zadd')
            ->with('queues:default:reserved', 1, ['job' => RedisQueueJob::class, 'data' => []]);

        $queue->setExpire(null);
        $queue->pop();
    }

    public function testExpireJobsWhenExpireSet()
    {
        $phpRedis = $this->createMock(\Redis::class);

        $redis = $this->getServiceMock(Redis::class, ['__invoke']);

        $redis->expects($this->any())
            ->method('__invoke')
            ->willReturn($phpRedis);

        $queue = $this->getServiceMock(RedisQueue::class, ['getTime', 'migrateAllExpiredJobs']);

        $queue->expects($this->once())
            ->method('getTime')
            ->willReturn(1);

        $queue->expects($this->once())
            ->method('migrateAllExpiredJobs')
            ->with('queues:default');

        $phpRedis->expects($this->once())
            ->method('lpop')
            ->with('queues:default')
            ->willReturn(['job' => RedisQueueJob::class, 'data' => []]);

        $phpRedis->expects($this->once())
            ->method('zadd')
            ->with('queues:default:reserved', 31, ['job' => RedisQueueJob::class, 'data' => []]);

        $queue->setExpire(30);
        $queue->pop();
    }
}
