<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;

/**
 * @mixin \QueueWorkerMixin
 */
class SyncQueue extends BaseQueue
{
    /**
     * @var array<BaseJob>
     */
    protected $jobs = [];

    /**
     * Whether to run the job manually
     *
     * @var bool
     */
    protected $manual = false;

    /**
     * {@inheritdoc}
     */
    public function pushJob(BaseJob $job): void
    {
        $this->jobs[] = $job;

        // Run directly
        if (!$this->manual) {
            $this->queueWorker->work([
                'queueName' => $job->getQueueName(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function push(string $job, $data = '', string $queue = null): void
    {
        $this->pushJob($this->createJob($this->createPayload($job, $data), $queue));
    }

    /**
     * {@inheritdoc}
     */
    public function pushRaw(array $payload, string $queue = null, array $options = []): void
    {
        $this->pushJob($this->createJob($payload, null, $queue));
    }

    /**
     * {@inheritdoc}
     */
    public function later($delay, $job, $data = '', $queue = null): void
    {
        $this->push($job, $data, $queue);
    }

    /**
     * {@inheritdoc}
     */
    public function pop(string $queue = null): ?BaseJob
    {
        foreach ($this->jobs as $i => $job) {
            if ($job->getQueueName() === $queue) {
                unset($this->jobs[$i]);
                return $job;
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($payload, $id = null): bool
    {
        foreach ($this->jobs as $i => $job) {
            if ($job->getId() === $id) {
                unset($this->jobs[$i]);
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->jobs = [];
    }
}
