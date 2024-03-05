<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Queue\BaseJob;

/**
 * 基于 Laravel Queue 简化的队列服务
 *
 * @link https://github.com/laravel/framework/tree/5.1/src/Illuminate/Queue
 */
abstract class BaseQueue extends BaseService
{
    /**
     * The name of the default queue.
     *
     * @var string
     */
    protected $name = 'default';
    /**
     * @var int
     */
    protected $time;

    /**
     * Returns the name of the default queue.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Push a job object onto the queue.
     *
     * @param BaseJob $job
     * @return mixed
     */
    public function pushJob(BaseJob $job): void
    {
        $this->push(get_class($job), $job->getPayload()['data'], $job->getQueueName(), [
            'delay' => $job->getDelay(),
        ]);
    }

    /**
     * Create a payload string from the given job and data.
     *
     * @param string $job
     * @param mixed $data
     * @param string|null $queue
     * @return array
     */
    protected function createPayload(string $job, $data = '', string $queue = null): array
    {
        return ['job' => $job, 'data' => $data];
    }

    /**
     * Create a job instance.
     *
     * @param array $payload
     * @param string|null $id
     * @param string|null $queue
     * @return BaseJob
     */
    protected function createJob(array $payload, string $id = null, string $queue = null): BaseJob
    {
        return new $payload['job']([
            'wei' => $this->wei,
            'id' => $id,
            'queue' => $this,
            'queueName' => $queue,
            'payload' => $payload,
        ]);
    }

    /**
     * Get the current UNIX timestamp.
     *
     * @return int
     */
    protected function getTime(): int
    {
        return $this->time ?: time();
    }

    public function setTime(int $time): self
    {
        $this->time = $time;
        return $this;
    }

    /**
     * @param mixed $value
     * @return false|string
     */
    protected function serialize($value)
    {
        return json_encode($value, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function unserialize($value)
    {
        return json_decode($value, true);
    }

    /**
     * Release the job back into the queue
     * @param array $payload
     * @param int $delay
     */
    public function release(array $payload, int $delay)
    {
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string $job
     * @param mixed $data
     * @param string|null $queue
     * @param array $options
     * @return void
     */
    abstract public function push(string $job, $data = '', string $queue = null, array $options = []): void;

    /**
     * Push a raw payload onto the queue.
     *
     * @param array $payload
     * @param string|null $queue
     * @param array $options
     * @return mixed
     */
    abstract public function pushRaw(array $payload, string $queue = null, array $options = []): void;

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTime|int $delay
     * @param string $job
     * @param mixed $data
     * @param string|null $queue
     * @return mixed
     */
    abstract public function later($delay, string $job, $data = '', string $queue = null): void;

    /**
     * Pop the next job off of the queue.
     *
     * @param string|null $queue
     * @return BaseJob|null
     */
    abstract public function pop(string $queue = null): ?BaseJob;

    /**
     * Delete the job from the queue.
     *
     * @param array $payload
     * @param int|null $id
     * @return bool
     */
    abstract public function delete(array $payload, int $id = null): bool;

    /**
     * Clear all jobs from the queue.
     *
     * @return void
     */
    abstract public function clear(): void;
}
