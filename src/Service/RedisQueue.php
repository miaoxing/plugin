<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;

/**
 * @mixin \RedisPropMixin
 * @mixin \TimePropMixin
 * @mixin \RandomPropMixin
 */
class RedisQueue extends BaseQueue
{
    /**
     * The expiration time of a job.
     *
     * @var int|null
     */
    protected $expire = 60;

    /**
     * {@inheritdoc}
     */
    public function pushRaw(array $payload, string $queue = null, array $options = []): string
    {
        if (isset($options['delay']) && $options['delay'] > 0) {
            $this->laterRaw($queue, $payload, $options['delay']);
        } else {
            $this->getRedis()->rpush($this->getQueue($queue), $this->serialize($payload));
        }

        return $payload['id'];
    }

    /**
     * {@inheritdoc}
     */
    public function release(array $payload, $delay): void
    {
        $payload['attempts'] = ($payload['attempts'] ?? 0) + 1;
        $this->getRedis()->zadd($this->getQueue() . ':delayed', $this->time->timestamp() + $delay, $this->serialize($payload));
    }

    /**
     * {@inheritdoc}
     */
    public function pop(string $name = null): ?BaseJob
    {
        $queue = $this->getQueue($name);

        if (null !== $this->expire) {
            $this->migrateAllExpiredJobs($queue);
        }

        $payload = $this->getRedis()->lpop($queue);
        if ($payload) {
            $this->getRedis()->zadd($queue . ':reserved', $this->time->timestamp() + $this->expire, $payload);
            $payload = $this->unserialize($payload);
            return $this->createJob($payload, $payload['id'], $name);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(array $payload, string $id = null): bool
    {
        return (bool) $this->getRedis()->zrem($this->getQueue() . ':reserved', $this->serialize($payload));
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        $this->redis->getObject()->del($this->getQueue());
        $this->redis->getObject()->del($this->getQueue() . ':delayed');
        $this->redis->getObject()->del($this->getQueue() . ':reserved');
    }

    /**
     * Get the expiration time in seconds.
     *
     * @return int|null
     */
    public function getExpire(): ?int
    {
        return $this->expire;
    }

    /**
     * Set the expiration time in seconds.
     *
     * @param int|null $seconds
     * @return void
     */
    public function setExpire(?int $seconds)
    {
        $this->expire = $seconds;
    }

    /**
     * Migrate all of the waiting jobs in the queue.
     *
     * @param string $queue
     * @return void
     */
    protected function migrateAllExpiredJobs(string $queue)
    {
        $this->migrateExpiredJobs($queue . ':delayed', $queue);
        $this->migrateExpiredJobs($queue . ':reserved', $queue);
    }

    /**
     * Migrate the delayed jobs that are ready to the regular queue.
     *
     * @param string $from
     * @param string $to
     * @return void
     */
    public function migrateExpiredJobs(string $from, string $to)
    {
        // TODO Use transaction
        // First we need to get all of jobs that have expired based on the current time
        // so that we can push them onto the main queue. After we get them we simply
        // remove them from this "delay" queues. All of this within a transaction.
        $jobs = $this->getExpiredJobs($from, $time = $this->time->timestamp());

        // If we actually found any jobs, we will remove them from the old queue and we
        // will insert them onto the new (ready) "queue". This means they will stand
        // ready to be processed by the queue worker whenever their turn comes up.
        if (count($jobs) > 0) {
            $this->removeExpiredJobs($from, $time);
            $this->pushExpiredJobsOntoNewQueue($to, $jobs);
        }
    }

    /**
     * Get the expired jobs from a given queue.
     *
     * @param string $from
     * @param int $time
     * @return array
     */
    protected function getExpiredJobs(string $from, int $time): array
    {
        return $this->getRedis()->zrangebyscore($from, '-inf', $time);
    }

    /**
     * Remove the expired jobs from a given queue.
     *
     * @param string $from
     * @param int $time
     * @return void
     */
    protected function removeExpiredJobs(string $from, int $time)
    {
        $this->getRedis()->zremrangebyscore($from, '-inf', $time);
    }

    /**
     * Push all of the given jobs onto another queue.
     *
     * @param string $to
     * @param array $jobs
     * @return void
     */
    protected function pushExpiredJobsOntoNewQueue(string $to, array $jobs)
    {
        call_user_func_array([$this->getRedis(), 'rpush'], array_merge([$to], $jobs));
    }

    /**
     * Push a raw payload onto the queue with a given delay.
     *
     * @param \DateTime|int $delay
     * @param array $payload
     * @param string|null $queue
     * @return string The id of the job
     */
    protected function laterRaw($delay, array $payload, string $queue = null): ?string
    {
        $this->getRedis()->zadd($this->getQueue($queue) . ':delayed', $this->time->timestamp() + $delay, $this->serialize($payload));
        return $payload['id'] ?? null;
    }

    /**
     * Create a payload string from the given job and data.
     *
     * @param string|BaseJob $job
     * @param mixed $data
     * @param string|null $queue
     * @return array
     */
    protected function createPayload($job, $data = '', string $queue = null): array
    {
        $payload = parent::createPayload($job, $data);
        $payload['id'] = $this->random->string(32);
        $payload['attempts'] = 0;
        return $payload;
    }

    /**
     * Get the queue or return the default.
     *
     * @param string|null $name
     * @return string
     */
    protected function getQueue(string $name = null): string
    {
        return 'queues:' . ($name ?: $this->name);
    }

    /**
     * Get the Redis instance.
     *
     * @return \Redis
     */
    protected function getRedis()
    {
        return $this->redis->getObject();
    }
}
