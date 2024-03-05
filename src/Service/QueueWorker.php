<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Queue\BaseJob;

/**
 * @mixin \CachePropMixin
 * @mixin \DbPropMixin
 * @mixin \EventPropMixin
 * @mixin \LoggerPropMixin
 * @mixin \QueuePropMixin
 */
class QueueWorker extends BaseService
{
    /**
     * @var string|null
     */
    protected $queueName;

    /**
     * Run the worker in daemon mode
     *
     * @var bool
     */
    protected $daemon = false;

    /**
     * Number of seconds to sleep when no job is available
     *
     * @var int
     */
    protected $sleep = 3;

    /**
     * Number of times to attempt a job before logging it failed
     *
     * @var int
     */
    protected $tries = 1;

    /**
     * Amount of time to delay failed jobs
     *
     * @var int
     */
    protected $delay = 0;

    /**
     * The memory limit in megabytes
     *
     * @var int
     */
    protected $memory = 100;

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
     * Run the worker instance.
     *
     * @param array $options
     * @return array{job: BaseJob, failed: bool}
     */
    public function work(array $options = []): array
    {
        $options && $this->setOption($options);

        if ($this->daemon) {
            return $this->daemon($this->queueName, $this->delay, $this->memory, $this->sleep, $this->tries);
        }

        return $this->pop($this->queueName, $this->delay, $this->sleep, $this->tries);
    }

    /**
     * Restart queue worker daemons after their current job
     */
    public function restart()
    {
        $this->cache->set('wei:queue:restart', $this->getTime());
    }

    /**
     * Listen to the given queue in a loop.
     *
     * @param string|null $queueName
     * @param int $delay
     * @param int $memory
     * @param int $sleep
     * @param int $maxTries
     * @return void
     */
    public function daemon(string $queueName = null, int $delay = 0, int $memory = 128, int $sleep = 3, int $maxTries = 0): void
    {
        $startTime = $this->getTime();
        $lastRestart = $this->getTimestampOfLastQueueRestart();

        while (true) {
            if ($this->daemonShouldRun()) {
                $this->runNextJobForDaemon($queueName, $delay, $sleep, $maxTries);
            } else {
                $this->sleep($sleep);
            }

            if ($this->timeExceeded($startTime, $this->timeLimit)
                || $this->memoryExceeded($memory)
                || $this->queueShouldRestart($lastRestart)) {
                $this->stop();
            }
        }
    }

    /**
     * Run the next job for the daemon worker.
     *
     * @param string $queueName
     * @param int $delay
     * @param int $sleep
     * @param int $maxTries
     */
    protected function runNextJobForDaemon(string $queueName, int $delay, int $sleep, int $maxTries)
    {
        try {
            $this->pop($queueName, $delay, $sleep, $maxTries);
        } catch (\Exception $e) {
            $this->logger->alert($e);
        }
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
     * Listen to the given queue.
     *
     * @param string|null $queueName
     * @param int $delay
     * @param int $sleep
     * @param int $maxTries
     * @return array
     */
    public function pop(string $queueName = null, int $delay = 0, int $sleep = 3, int $maxTries = 0): array
    {
        $job = $this->getNextJob($this->queue, $queueName);

        // If we're able to pull a job off of the stack, we will process it and
        // then immediately return back out. If there is no job on the queue
        // we will "sleep" the worker for the specified number of seconds.
        if (null !== $job) {
            return $this->process($job, $maxTries, $delay);
        }

        $this->sleep($sleep);

        return ['job' => null, 'failed' => false];
    }

    /**
     * Get the next job from the queue driver.
     *
     * @param BaseQueue $driver
     * @param string|null $queueName
     * @return BaseJob|null
     */
    protected function getNextJob(BaseQueue $driver, string $queueName = null): ?BaseJob
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
     * @return array
     */
    public function process(BaseJob $job, int $maxTries = 0, int $delay = 0): array
    {
        try {
            // First we will fire off the job. Once it is done we will see if it will
            // be auto-deleted after processing and if so we will go ahead and run
            // the delete method on the job. Otherwise we will just keep moving.
            $job->fire();
            $this->raiseAfterJobEvent($job);

            return ['job' => $job, 'failed' => false];
        } catch (\Exception $e) {
            if ($maxTries > 0 && $job->attempts() + 1 >= $maxTries) {
                // If the maximum number of retries has been reached, we will log and delete the job
                $this->logFailedJob($job, $e);
            } elseif (!$job->isDeleted()) {
                // Otherwise we release back to the queue so we can try running again
                $job->release($delay);
            }

            return ['job' => $job, 'failed' => true];
        }
    }

    /**
     * Raise the after queue job event.
     *
     * @param BaseJob $job
     */
    protected function raiseAfterJobEvent(BaseJob $job)
    {
        $this->event->trigger('queueAfter', $job);
    }

    /**
     * Log a failed job into storage.
     *
     * @param BaseJob $job
     * @param \Exception $e
     * @return void
     */
    protected function logFailedJob(BaseJob $job, \Exception $e): void
    {
        $this->logger->alert('Queue job failed', $job->getPayload());

        if ($this->logFailedJobsToDb) {
            $this->db->insert('queue_failed_jobs', [
                'driver' => $this->queue->getDriver(),
                'queue' => $job->getQueueName() ?? $this->queue->getName(),
                'payload' => json_encode($job->getPayload(), \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE),
                'exception' => (string)$e,
                'created_at' => date('Y-m-d H:i:s', $this->getTime()),
            ]);
        }

        $job->delete();
        $job->failed();
        $this->raiseFailedJobEvent($job);
    }

    /**
     * Raise the failed queue job event.
     *
     * @param BaseJob $job
     */
    protected function raiseFailedJobEvent(BaseJob $job)
    {
        $this->event->trigger('queueFailed', [$job, $job->getPayload()]);
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
        return (bool)$this->db->delete('queue_failed_jobs', ['id' => $id]);
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
        return $this->getTime() - $startTime > $timeLimit;
    }

    /**
     * Stop listening and bail out of the script.
     */
    public function stop()
    {
        $this->event->trigger('queueStopping');
        exit;
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param int $seconds
     */
    public function sleep(int $seconds)
    {
        sleep($seconds);
    }

    /**
     * @param int $sleep
     * @return self
     */
    public function setSleep(int $sleep): self
    {
        $this->sleep = $sleep;
        return $this;
    }

    /**
     * @return int
     */
    public function getSleep(): int
    {
        return $this->sleep;
    }

    /**
     * @param int $tries
     * @return $this
     */
    public function setTries(int $tries): self
    {
        $this->tries = $tries;
        return $this;
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
     * Get the last queue restart timestamp, or null.
     *
     * @return int
     */
    protected function getTimestampOfLastQueueRestart(): int
    {
        return (int)$this->cache->get('wei:queue:restart');
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
