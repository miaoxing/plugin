<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;

class Queue extends BaseQueue
{
    protected $driver = 'dbQueue';

    public function dispatch(BaseJob $job)
    {
        return $this->getObject()->dispatch($job);
    }

    public function push(string $job, $data = '', string $queue = null)
    {
        return $this->getObject()->push($job, $data, $queue);
    }

    public function pushRaw(array $payload, string $queue = null, array $options = [])
    {
        return $this->getObject()->pushRaw($payload, $queue, $options);
    }

    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->getObject()->later($delay, $job, $data, $queue);
    }

    public function pop($queue = null): ?BaseJob
    {
        return $this->getObject()->pop($queue);
    }

    public function delete($payload, $id = null)
    {
        return $this->getObject()->delete($payload, $id);
    }

    public function getObject(): BaseQueue
    {
        return $this->{$this->driver};
    }

    public function setDriver(string $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    public function clear(): void
    {
        $this->getObject()->clear();
    }
}
