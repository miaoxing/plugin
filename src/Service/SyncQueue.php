<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;

/**
 * @mixin \QueueWorkerPropMixin
 * @mixin \RandomPropMixin
 * @mixin \LoggerPropMixin
 */
class SyncQueue extends BaseQueue
{
    /**
     * @var array<array>
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
    public function pushRaw(array $payload, ?string $queue = null, $delay = 0): string
    {
        if ($delay > 0) {
            $this->logger->info('Sync queue does not support delay', $payload);
        }

        $queue = $this->getName($queue);
        $id = $this->random->string(32);
        $this->jobs[] = [
            'id' => $id,
            'queue' => $queue,
            'payload' => $payload,
        ];

        // Run directly
        if (!$this->manual) {
            $this->queueWorker->runNextJob([
                'queueName' => $queue,
            ]);
        }

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function pop(?string $name = null): ?BaseJob
    {
        $name = $this->getName($name);
        foreach ($this->jobs as $i => $job) {
            if ($job['queue'] === $name) {
                unset($this->jobs[$i]);
                $payload = $job['payload'];
                $payload['attempts'] = ($payload['attempts'] ?? 0) + 1;
                return $this->createJob($payload, $job['id'], $name);
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $id): bool
    {
        foreach ($this->jobs as $i => $job) {
            if ($job['id'] === $id) {
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

    /**
     * @param bool $manual
     * @return SyncQueue
     */
    public function setManual(bool $manual): self
    {
        $this->manual = $manual;
        return $this;
    }
}
