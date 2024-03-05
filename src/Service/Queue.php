<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;

class Queue extends BaseQueue
{
    /**
     * @var string
     */
    protected $driver = 'syncQueue';

    public function pushJob(BaseJob $job): void
    {
        $this->getObject()->pushJob($job);
    }

    public function push(string $job, $data = '', string $queue = null): void
    {
        $this->getObject()->push($job, $data, $queue);
    }

    public function pushRaw(array $payload, string $queue = null, array $options = []): void
    {
        $this->getObject()->pushRaw($payload, $queue, $options);
    }

    public function later($delay, $job, $data = '', $queue = null): void
    {
        $this->getObject()->later($delay, $job, $data, $queue);
    }

    public function pop($queue = null): ?BaseJob
    {
        return $this->getObject()->pop($queue);
    }

    public function delete($payload, $id = null): bool
    {
        return $this->getObject()->delete($payload, $id);
    }

    public function clear(): void
    {
        $this->getObject()->clear();
    }

    public function setDriver(string $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getObject(): BaseQueue
    {
        return $this->{$this->driver};
    }
}
