<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Queue\BaseJob;

/**
 * The base class for queue service
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
     * Get the queue name or return the default name.
     *
     * @param string|null $name
     * @return string
     */
    public function getName(?string $name = null): string
    {
        return $name ?: $this->name;
    }

    /**
     * Push a new job onto the queue.
     *
     * @param string|BaseJob $job The job class name or the job object
     * @param mixed $data
     * @param string|null $queue
     * @param int $delay
     * @return string The id of the job
     */
    public function push($job, $data = '', ?string $queue = null, $delay = 0): string
    {
        if ($job instanceof BaseJob) {
            // NOTE: $data, $queue and $delay arguments are ignored
            $data = $this->getPayloadData($job);
            $queue = $job->getQueueName();
            $delay = $job->getDelay();
            $name = get_class($job);
        } else {
            $name = $job;
        }

        $id = $this->pushRaw($this->createPayload($name, $data), $queue, $delay);

        if ($job instanceof BaseJob) {
            $job->setJobId($id);
        }

        return $id;
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param array $payload
     * @param string|null $queue
     * @param int $delay
     * @return string The id of the job
     */
    abstract public function pushRaw(array $payload, ?string $queue = null, $delay = 0): string;

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param int $delay
     * @param string|BaseJob $job
     * @param mixed $data
     * @param string|null $queue
     * @return string The id of the job
     */
    public function later($delay, $job, $data = '', ?string $queue = null): string
    {
        return $this->push($job, $data, $queue, $delay);
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string|null $name The name of the queue
     * @return BaseJob|null
     */
    abstract public function pop(?string $name = null): ?BaseJob;

    /**
     * Delete the job from the queue.
     *
     * @param string $id
     * @return bool
     */
    abstract public function delete(string $id): bool;

    /**
     * Clear all jobs from the queue.
     *
     * @return void
     */
    abstract public function clear(): void;

    /**
     * Release the job back into the queue
     *
     * @param string $queue
     * @param BaseJob $job
     * @param int $delay
     * @return void
     */
    public function release(string $queue, BaseJob $job, $delay): void
    {
        $this->delete($job->getJobId());
        $this->pushRaw($job->getPayload(), $queue, $delay);
    }

    /**
     * Create a payload string from the given job and data.
     *
     * @param string|BaseJob $job
     * @param mixed $data
     * @param string|null $queue Allow to custom payload by queue name
     * @return array
     */
    protected function createPayload($job, $data = '', ?string $queue = null): array
    {
        return ['job' => $job, 'data' => $data];
    }

    /**
     * Create a job instance.
     *
     * @param array{job: class-string<BaseJob>, data?: array} $payload
     * @param string $id
     * @param string $queue
     * @return BaseJob
     * @experimental
     */
    protected function createJob(array $payload, string $id, string $queue): BaseJob
    {
        // Convert payload data to object properties
        $properties = [];
        if (isset($payload['data'])) {
            foreach ($payload['data'] as $name => $value) {
                $properties[$name] = $value;
            }
        }

        // NOTE: __construct is not called
        /** @var BaseJob $job */
        $job = (new \ReflectionClass($payload['job']))->newInstanceWithoutConstructor();
        $job->setOption(
            [
                'wei' => $this->wei,
                'jobId' => $id,
                'queue' => $this,
                'queueName' => $queue,
                'payload' => $payload,
            ] + $properties
        );
        return $job;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function serialize($value): string
    {
        return serialize($value);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    protected function unserialize($value)
    {
        return unserialize($value);
    }

    /**
     * Get payload data from the protected properties of the object
     *
     * @param BaseJob $job
     * @return array
     * @internal
     */
    protected function getPayloadData(BaseJob $job): array
    {
        $data = [];
        $class = get_class($job);
        $props = (new \ReflectionClass($job))->getProperties(\ReflectionProperty::IS_PROTECTED);
        foreach ($props as $prop) {
            if ($prop->class === $class) {
                $prop->setAccessible(true);
                $data[$prop->getName()] = $prop->getValue($job);
            }
        }
        return $data;
    }
}
