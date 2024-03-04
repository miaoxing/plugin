<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;

class SyncQueue extends BaseQueue
{
    public function dispatch(BaseJob $job)
    {
        $this->jobs[] = $job;
    }

    /**
     * {@inheritdoc}
     */
    public function push(string $job, $data = '', string $queue = null)
    {
        $this->jobs[] = $this->createJob($this->createPayload($job, $data, $queue));

        // TODO sync 什么时候触发？
//        $this->queueWorker->work([
//            'queueName' => $queue,
//        ]);
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function pushRaw(array $payload, string $queue = null, array $options = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->push($job, $data, $queue);
    }

    /**
     * {@inheritdoc}
     */
    public function pop($queue = null): ?BaseJob
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
    public function delete($payload, $id = null)
    {
    }

    public function clear(): void
    {
        $this->jobs = [];
    }
}
