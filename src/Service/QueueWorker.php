<?php

namespace Miaoxing\Plugin\Service;

use Illuminate\Queue\Jobs\Job;
use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Queue\BaseJob;

/**
 * @mixin \CachePropMixin
 * @mixin \DbPropMixin
 * @mixin \EventPropMixin
 * @mixin \LoggerPropMixin
 * @mixin \QueuePropMixin
 * @mixin \TimePropMixin
 */
class QueueWorker extends BaseService
{
    /**
     * @var string|null
     */
    protected $queueName;

    /**
     * Number of seconds to sleep when no job is available
     *
     * @var int|float
     */
    protected $sleep = 3;

    /**
     * Number of times to attempt a job before logging it failed
     *
     * @var int
     */
    protected $maxTries = 1;

    /**
     * Amount of time to delay failed jobs
     *
     * @var int
     */
    protected $delay = 0;

    /**
     * The job number to run a daemon
     *
     * @var int
     */
    protected $jobLimit = 0;

    /**
     * The memory limit in megabytes
     *
     * @var int
     */
    protected $memoryLimit = 100;

    /**
     * The time limit to run a daemon
     *
     * @var int
     */
    protected $timeLimit = 1800;

    /**
     * Whether log failed jobs to database or not
     *
     * @var bool
     */
    protected $logFailedJobsToDb = true;

    /**
     * Listen to the given queue in a loop.
     *
     * @param array $options
     * @return void
     */
    public function run(array $options = []): void
    {
        $options && $this->setOption($options);

        $startTime = $this->time->timestamp();
        $lastRestart = $this->getTimestampOfLastQueueRestart();

        $jobCount = 0;
        while (true) {
            $job = null;
            if ($this->daemonShouldRun()) {
                $job = $this->runNextJobForDaemon();
            }

            if ($job) {
                ++$jobCount;
            } else {
                $this->sleep($this->sleep);
            }

            if (
                $this->jobLimitExceeded($jobCount)
                || $this->timeExceeded($startTime, $this->timeLimit)
                || $this->memoryExceeded($this->memoryLimit)
                || $this->queueShouldRestart($lastRestart)
            ) {
                $this->triggerAfterQueueStopEvent();
                return;
            }
        }
    }

    /**
     * Run the next job on the queue
     *
     * @param array $options
     * @return BaseJob|null
     */
    public function runNextJob(array $options = []): ?BaseJob
    {
        $options && $this->setOption($options);

        $job = $this->getNextJob($this->queue, $this->queueName);

        // If we're able to pull a job off of the stack, we will process it and
        // then immediately return back out. If there is no job on the queue
        // we will "sleep" the worker for the specified number of seconds.
        if (null !== $job) {
            $this->runJob($job, $this->maxTries, $this->delay);
        }

        return $job;
    }

    /**
     * Restart queue worker daemons after their current job
     */
    public function restart()
    {
        $this->cache->set('wei:queue:restart', $this->time->timestamp());
    }

    /**
     * Retry failed job.
     *
     * @param mixed $id
     * @return array
     */
    public function retry($id): array
    {
        $condition = 'all' === $id ? false : ['id' => $id];
        $jobs = wei()->db->selectAll('queue_failed_jobs', $condition);
        if (!$jobs) {
            return ['code' => -1, 'message' => 'BaseJob not found'];
        }

        foreach ($jobs as $job) {
            $payload = json_decode($job['payload'], true);
            $this->queue->push($payload['job'], $payload['data'], $job['queue']);
            $this->forget($job['id']);
        }

        return ['code' => 1, 'message' => 'Operation successful'];
    }

    /**
     * Delete a failed job from database.
     *
     * @param int $id
     * @return bool
     */
    public function forget(int $id): bool
    {
        return (bool) $this->db->delete('queue_failed_jobs', ['id' => $id]);
    }

    /**
     * Determine if the job limit has been exceeded.
     *
     * @param int $jobLimit
     * @return bool
     */
    public function jobLimitExceeded(int $jobLimit): bool
    {
        return $this->jobLimit && $this->jobLimit <= $jobLimit;
    }

    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param int $memoryLimit
     * @return bool
     */
    public function memoryExceeded(int $memoryLimit): bool
    {
        return (memory_get_usage() / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Determine if the time limit has been exceeded.
     *
     * @param int $startTime
     * @param int $timeLimit
     * @return bool
     */
    public function timeExceeded(int $startTime, int $timeLimit): bool
    {
        return $this->time->timestamp() - $startTime >= $timeLimit;
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param int|float $seconds
     */
    public function sleep($seconds)
    {
        sleep($seconds);
    }

    /**
     * @param int|float $sleep
     * @return self
     */
    public function setSleep($sleep): self
    {
        $this->sleep = $sleep;
        return $this;
    }

    /**
     * @return int|float
     */
    public function getSleep()
    {
        return $this->sleep;
    }

    /**
     * @param int $maxTries
     * @return $this
     */
    public function setMaxTries(int $maxTries): self
    {
        $this->maxTries = $maxTries;
        return $this;
    }

    /**
     * Run the next job for the daemon worker.
     */
    protected function runNextJobForDaemon(): ?BaseJob
    {
        try {
            return $this->runNextJob();
        } catch (\Exception $e) {
            $this->logger->alert($e);
            return null;
        }
    }

    /**
     * Get the next job from the queue driver.
     *
     * @param BaseQueue $driver
     * @param string|null $queueName
     * @return BaseJob|null
     */
    protected function getNextJob(BaseQueue $driver, ?string $queueName = null): ?BaseJob
    {
        if (null === $queueName) {
            return $driver->pop();
        }

        foreach (explode(',', $queueName) as $queue) {
            if (null !== ($job = $driver->pop($queue))) {
                return $job;
            }
        }

        return null;
    }

    /**
     * Process a given job from the queue.
     *
     * @param BaseJob $job
     * @param int $maxTries
     * @param int $delay
     */
    protected function runJob(BaseJob $job, int $maxTries = 0, int $delay = 0): void
    {
        try {
            // First we will fire off the job. Once it is done we will see if it will
            // be auto-deleted after processing and if so we will go ahead and run
            // the delete method on the job. Otherwise we will just keep moving.
            $this->triggerBeforeJobProcessEvent($job);

            $job->__invoke();

            $this->triggerAfterJobProcessEvent($job);

            // Auto delete if no error
            if (!$job->isDeletedOrReleased()) {
                $job->delete();
            }
        } catch (\Throwable $e) {
            if ($maxTries > 0 && $job->attempts() >= $maxTries) {
                // If the maximum number of retries has been reached, we will log and delete the job
                $this->handleJobFailed($job, $e);
            } elseif (!$job->isDeleted()) {
                // Otherwise we release back to the queue so we can try running again
                $job->release($delay);
            }
        }
    }

    /**
     * Handle a job failure.
     *
     * @param BaseJob $job
     * @param \Throwable $e
     * @return void
     */
    protected function handleJobFailed(BaseJob $job, \Throwable $e): void
    {
        $this->logger->alert('Queue job failed', $job->getPayload());

        if ($this->logFailedJobsToDb) {
            $this->db->insert('queue_failed_jobs', [
                'driver' => $this->queue->getDriver(),
                'queue' => $job->getQueueName() ?? $this->queue->getName(),
                'payload' => json_encode($job->getPayload(), \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE),
                'exception' => (string) $e,
                'created_at' => date('Y-m-d H:i:s', $this->time->timestamp()),
            ]);
        }

        $job->setFailed();
        $job->delete();
        $job->failed();
        $this->triggerQueueJobFailEvent($job);
    }

    /**
     * Raise the after queue job event.
     *
     * @param BaseJob $job
     */
    protected function triggerBeforeJobProcessEvent(BaseJob $job)
    {
        $this->event->trigger('beforeQueueJobProcess', $job);
    }

    /**
     * Raise the after queue job event.
     *
     * @param BaseJob $job
     */
    protected function triggerAfterJobProcessEvent(BaseJob $job)
    {
        $this->event->trigger('afterQueueJobProcess', $job);
    }

    /**
     * Raise the failed queue job event.
     *
     * @param BaseJob $job
     */
    protected function triggerQueueJobFailEvent(BaseJob $job)
    {
        $this->event->trigger('queueJobFail', $job);
    }

    /**
     * Stop listening and bail out of the script.
     */
    protected function triggerAfterQueueStopEvent()
    {
        $this->event->trigger('afterQueueStop');
    }

    /**
     * Determine if the daemon should process on this iteration.
     *
     * @return bool
     */
    protected function daemonShouldRun(): bool
    {
        return false !== $this->event->until('queueLooping');
    }

    /**
     * Get the last queue restart timestamp, or null.
     *
     * @return int
     */
    protected function getTimestampOfLastQueueRestart(): int
    {
        return (int) $this->cache->get('wei:queue:restart');
    }

    /**
     * Determine if the queue worker should restart.
     *
     * @param int|null $lastRestart
     * @return bool
     */
    protected function queueShouldRestart(?int $lastRestart): bool
    {
        return $this->getTimestampOfLastQueueRestart() != $lastRestart;
    }
}
