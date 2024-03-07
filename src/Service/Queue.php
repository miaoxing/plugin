<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;

/**
 * The wrapper class for queue service
 *
 * Simplified queue service based on Laravel Queue
 *
 * @link https://laravel.com/docs/10.x/queues
 */
class Queue extends BaseQueue
{
    /**
     * The queue driver name
     *
     * @var string
     */
    protected $driver = 'syncQueue';

    /**
     * {@inheritDoc}
     */
    public function push($job, $data = '', ?string $queue = null, $delay = 0): string
    {
        return $this->getObject()->push($job, $data, $queue, $delay);
    }

    /**
     * {@inheritDoc}
     */
    public function pushRaw(array $payload, ?string $queue = null, $delay = 0): string
    {
        return $this->getObject()->pushRaw($payload, $queue, $delay);
    }

    /**
     * {@inheritDoc}
     */
    public function pop(?string $name = null): ?BaseJob
    {
        return $this->getObject()->pop($name);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $id): bool
    {
        return $this->getObject()->delete($id);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        $this->getObject()->clear();
    }

    /**
     * Set the queue driver name
     *
     * @param string $driver
     * @return $this
     */
    public function setDriver(string $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Return the queue driver name
     *
     * @return string
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Return the queue object
     *
     * @return BaseQueue
     */
    public function getObject(): BaseQueue
    {
        return $this->{$this->driver};
    }
}
