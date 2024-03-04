<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Queue\BaseJob;

/**
 * 基于 Laravel Queue 简化的队列服务
 *
 * @link https://github.com/laravel/framework/tree/5.1/src/Illuminate/Queue
 * @mixin \EventPropMixin
 * @mixin \QueueWorkerPropMixin
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
     * @var array<BaseJob>
     */
    protected $jobs = [];

    public function dispatch(BaseJob $job)
    {
        return $this->push(get_class($job), $job->getPayload()['data'], $job->getQueueName());
    }

    public function __destruct()
    {
        foreach ($this->jobs as $job) {
            $this->push(get_class($job), $job->getPayload());
        }
        $this->jobs = [];
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string $queue
     * @param string $job
     * @param mixed $data
     * @return mixed
     */
    public function pushOn(string $queue, string $job, $data = '')
    {
        return $this->push($job, $data, $queue);
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param string $queue
     * @param \DateTime|int $delay
     * @param string $job
     * @param mixed $data
     * @return mixed
     */
    public function laterOn(string $queue, $delay, string $job, $data = '')
    {
        return $this->later($delay, $job, $data, $queue);
    }

    /**
     * Push an array of jobs with different jobs and same data.
     *
     * @param array $jobs
     * @param mixed $data
     * @param string|null $queue
     * @return void
     */
    public function bulk(array $jobs, $data = '', string $queue = null)
    {
        foreach ($jobs as $job) {
            $this->push($job, $data, $queue);
        }
    }

    /**
     * Push an array of jobs with different data and some jobs.
     *
     * @param string $job
     * @param array $data
     * @param string|null $queue
     */
    public function pushMulti(string $job, array $data = [], string $queue = null)
    {
        foreach ($data as $row) {
            $this->push($job, $row, $queue);
        }
    }

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
     * Set additional meta on a payload string.
     *
     * @param array $payload
     * @param string $key
     * @param mixed $value
     * @return array
     */
    protected function setMeta(array $payload, string $key, $value): array
    {
        $payload[$key] = $value;
        return $payload;
    }

    /**
     * Create a job instance.
     *
     * @param array $payload
     * @param string|null $id
     * @param string|null $queue
     * @return \Miaoxing\Plugin\Queue\BaseJob
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
     * Calculate the number of seconds with the given delay.
     *
     * @param \DateTime|int $delay
     * @return int
     */
    protected function getSeconds($delay): int
    {
        if ($delay instanceof \DateTime) {
            return max(0, $delay->getTimestamp() - $this->getTime());
        }
        return (int)$delay;
    }

    /**
     * Get the current UNIX timestamp.
     *
     * @return int
     */
    protected function getTime(): int
    {
        return time();
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
     * @return mixed
     */
    abstract public function push(string $job, $data = '', string $queue = null);

    /**
     * Push a raw payload onto the queue.
     *
     * @param array $payload
     * @param string|null $queue
     * @param array $options
     * @return mixed
     */
    abstract public function pushRaw(array $payload, string $queue = null, array $options = []);

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTime|int $delay
     * @param string $job
     * @param mixed $data
     * @param string|null $queue
     * @return mixed
     */
    abstract public function later($delay, string $job, $data = '', string $queue = null);

    /**
     * Pop the next job off of the queue.
     *
     * @param string|null $queue
     * @return \Miaoxing\Plugin\Queue\BaseJob|null
     */
    abstract public function pop(string $queue = null): ?BaseJob;

    /**
     * Delete the job from the queue.
     *
     * @param array $payload
     * @param int|null $id
     */
    abstract public function delete(array $payload, int $id = null);

    /**
     * Clear all jobs from the queue.
     *
     * @return void
     */
    abstract public function clear(): void;
}
