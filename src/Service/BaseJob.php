<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;

/**
 * @property BaseQueue $queue
 */
class BaseJob extends BaseService
{
    /**
     * @var array
     */
    protected $payload = [];

    /**
     * The job handler instance.
     *
     * @var mixed
     */
    protected $instance;

    /**
     * The name of the queue the job belongs to.
     *
     * @var string
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
     * Fire the job.
     *
     * @return void
     */
    public function fire()
    {
        $this->instance = new $this->payload['job']([
            'wei' => $this->wei,
        ]);
        $this->instance->__invoke($this, $this->payload['data']);
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
        return $this->payload['attempts'];
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
     * Call when the job is failed
     *
     * @return void
     */
    public function failed()
    {
        if (method_exists($this->instance, 'failed')) {
            $this->instance->failed();
        }
    }

    /**
     * Calculate the number of seconds with the given delay.
     *
     * @param  \DateTime|int $delay
     * @return int
     */
    protected function getSeconds($delay): int
    {
        if ($delay instanceof \DateTime) {
            return max(0, $delay->getTimestamp() - $this->getTime());
        }
        return (int) $delay;
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
     * Get the name of current queue
     *
     * @return string
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }
}
