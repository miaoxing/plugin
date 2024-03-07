<?php

namespace Miaoxing\Plugin\Queue;

use Miaoxing\Plugin\BaseService;

/**
 * @mixin \LoggerPropMixin
 * @mixin \QueuePropMixin
 */
abstract class BaseJob extends BaseService
{
    /**
     * The id of the job in the queue, not the current object id
     *
     * @var string|null
     */
    protected $jobId;

    /**
     * The name of the queue the job belongs to.
     *
     * @var string|null
     */
    protected $queueName;

    /**
     * The data of the job.
     *
     * @var array
     */
    protected $payload = [];

    /**
     * Indicates if the job has been deleted.
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * Indicates if the job has been released.
     *
     * @var bool
     */
    protected $released = false;

    /**
     * Indicates if the job is failed.
     *
     * @var bool
     */
    protected $failed = false;

    /**
     * {@inheritdoc}
     */
    public static function __callStatic(string $method, array $args)
    {
        if ('dispatch' === $method) {
            // Pass dispatch arguments to the constructor
            return (new static(...$args))->handleDispatch();
        } else {
            return parent::__callStatic($method, $args);
        }
    }

    /**
     * Run the job
     *
     * @return void
     */
    abstract public function __invoke(): void;

    /**
     * Call when the job is failed
     *
     * @return void
     */
    public function failed(): void
    {
    }

    /**
     * Set the job as failed.
     *
     * @return void
     */
    public function setFailed()
    {
        $this->failed = true;
    }

    /**
     * Determine if the job is failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->failed;
    }

    /**
     * @param string $jobId
     * @return $this
     */
    public function setJobId(string $jobId): self
    {
        $this->jobId = $jobId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getJobId(): ?string
    {
        return $this->jobId;
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete(): void
    {
        $this->queue->delete($this->jobId);
        $this->deleted = true;
    }

    /**
     * Determine if the job has been deleted.
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * Release the job back into the queue.
     *
     * @param \DateTime|int $delay
     * @return void
     */
    public function release($delay = 0): void
    {
        $this->queue->release($this->queueName, $this, $delay);
        $this->released = true;
    }

    /**
     * Determine if the job was released back into the queue.
     *
     * @return bool
     */
    public function isReleased(): bool
    {
        return $this->released;
    }

    /**
     * Determine if the job has been deleted or released.
     *
     * @return bool
     */
    public function isDeletedOrReleased(): bool
    {
        return $this->isDeleted() || $this->isReleased();
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts(): int
    {
        return $this->payload['attempts'] ?? 1;
    }

    /**
     * Get the payload array for the job.
     *
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param array $payload
     * @return $this
     */
    public function setPayload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * Set the delay second of current job
     *
     * @param \DateTime|int $seconds
     * @return $this
     */
    public function delay($seconds): self
    {
        $this->payload['delay'] = $seconds;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDelay()
    {
        return $this->payload['delay'] ?? 0;
    }

    /**
     * Get the name of current queue
     *
     * @return string|null
     */
    public function getQueueName(): ?string
    {
        return $this->queueName;
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        $data = [];
        $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PROTECTED);
        foreach ($properties as $property) {
            if ($property->class !== static::class) {
                continue;
            }

            $value = $this->{$property->name};
            $data[$property->name] = $value;
        }

        return $data;
    }

    /**
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        foreach ($data as $name => $value) {
            $this->{$name} = $value;
        }
    }

    /**
     * Dispatch the current job with the given arguments.
     *
     * @svc
     */
    protected function dispatch(...$args): self
    {
        return $this->handleDispatch(func_get_args());
    }

    /**
     * Set the queue name of current job
     *
     * @param string $name
     * @return $this
     * @svc
     */
    protected function onQueue(string $name): self
    {
        $this->queueName = $name;
        return $this;
    }

    /**
     * @param array $args
     * @return $this
     */
    protected function handleDispatch(array $args = []): self
    {
        if (func_get_args()) {
            $this->setProperties($args);
        }

        $this->queue->push($this);
        return $this;
    }

    /**
     * Set the parameters of the `dispatch` method to properties of the object
     *
     * @param array $args
     * @return $this
     * @experimental
     */
    protected function setProperties(array $args): self
    {
        $method = new \ReflectionMethod($this, '__construct');
        foreach ($method->getParameters() as $i => $parameter) {
            if (isset($args[$i])) {
                $this->{$parameter->getName()} = $args[$i];
            } elseif ($parameter->isDefaultValueAvailable()) {
                $this->{$parameter->getName()} = $parameter->getDefaultValue();
            }
        }
        return $this;
    }
}
