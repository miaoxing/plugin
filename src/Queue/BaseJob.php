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
     * @var array
     */
    protected $payload = [];

    /**
     * The name of the queue the job belongs to.
     *
     * @var string|null
     */
    protected $queueName;

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
     * @var int
     */
    protected $id;

    /**
     * @return $this
     * @svc
     */
    protected function enqueue(...$args): self
    {
        $this->payload['data'] = $args;

        $this->queue->pushJob($this);
        return $this;
    }

    /**
     * Dispatch the current job with the given arguments.
     *
     * @param mixed ...$args
     * @return static
     */
    public static function dispatch(...$args): self
    {
        return new static([
            'payload' => [
                'data' => $args,
            ],
        ]);
    }

    /**
     * Dispatch the current job with the given arguments if the given condition is true.
     *
     * @param mixed $condition
     * @param mixed ...$args
     * @return BaseJob|void
     */
    public static function dispatchIf($condition, ...$args)
    {
        if ($condition) {
            return static::dispatch(...$args);
        }
    }

    /**
     * Dispatch the current job with the given arguments unless the given condition is true.
     *
     * @param mixed $condition
     * @param mixed ...$args
     * @return BaseJob|void
     */
    public static function dispatchUnless($condition, ...$args)
    {
        if (!$condition) {
            return static::dispatch(...$args);
        }
    }

    abstract public function __invoke($data);

    /**
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        $this->__invoke($this->payload['data']);

        // Auto delete if no error
        if (!$this->isDeletedOrReleased()) {
            $this->delete();
        }
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        $this->queue->delete($this->payload, $this->id);
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
     * @param int $delay
     * @return void
     */
    public function release(int $delay = 0)
    {
        $this->queue->release($this->payload, $delay);
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
        return $this->payload['attempts'] ?? 0;
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
     * Call when the job is failed
     *
     * @return void
     */
    public function failed()
    {
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
     * Get the current system time.
     *
     * @return int
     */
    protected function getTime(): int
    {
        return time();
    }

    /**
     * Get the name of the queued job class.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->payload['job'];
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
     * Get the name of current queue
     *
     * @return string|null
     */
    public function getQueueName(): ?string
    {
        return $this->queueName;
    }

    /**
     * Set the delay second of current job
     *
     * @param int $seconds
     * @return $this
     */
    public function delay(int $seconds): self
    {
        $this->payload['delay'] = $seconds;
        return $this;
    }

    /**
     * @return int
     */
    public function getDelay(): int
    {
        return $this->payload['delay'] ?? 0;
    }

//    public function __destruct()
//    {
//        if (!$this->isDeleted()) {
//            $this->queue->pushJob($this);
//        }
//    }
}
