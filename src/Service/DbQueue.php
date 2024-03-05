<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;
use Wei\QueryBuilder;

/**
 * @mixin \DbPropMixin
 */
class DbQueue extends BaseQueue
{
    /**
     * The database table that holds the jobs.
     *
     * @var string
     */
    protected $table = 'queue_jobs';

    /**
     * The expiration time of a job.
     *
     * @var int|null
     */
    protected $expire = 60;

    /**
     * {@inheritdoc}
     */
    public function push(string $job, $data = '', string $queue = null, array $options = []): void
    {
        $this->pushRaw($this->createPayload($job, $data), $queue, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function pushRaw(array $payload, string $queue = null, array $options = []): void
    {
        $createdAt = $availableAt = $this->getTime();
        if (isset($options['delay'])) {
            $availableAt += $options['delay'];
        }

        $this->db->insert($this->table, [
            'queue' => $this->getQueue($queue),
            'payload' => $this->serialize($payload),
            'created_at' => date('Y-m-d H:i:s', $createdAt),
            'available_at' => date('Y-m-d H:i:s', $availableAt),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function later($delay, $job, $data = '', $queue = null): void
    {
        $payload = $this->createPayload($job, $data);

        $this->pushRaw($payload, $queue, ['delay' => $delay]);
    }

    /**
     * {@inheritdoc}
     */
    public function pop(string $queue = null): ?BaseJob
    {
        $queue = $this->getQueue($queue);

        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        if ($job = $this->getNextAvailableJob($queue)) {
            $job = $this->markJobAsReserved($job);

            $pdo->commit();

            return $this->createJob($job['payload'], $job['id'], $queue);
        }

        $pdo->commit();
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($payload, $id = null): bool
    {
        return (bool)$this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->db->delete($this->table, []);
    }

    /**
     * Get the queue or return the default.
     *
     * @param string|null $queue
     * @return string
     */
    protected function getQueue(string $queue = null): string
    {
        return $queue ?: $this->name;
    }

    /**
     * Get the next available job for the queue.
     *
     * @param string|null $queue
     * @return array|null
     */
    protected function getNextAvailableJob(?string $queue): ?array
    {
        $time = $this->getTime();
        $job = QueryBuilder::table($this->table)
            ->where(['queue' => $this->getQueue($queue)])
            ->whereRaw(
            // available or reserved but expired
                '(reserved_at IS NULL AND available_at <= ?) OR (reserved_at <= ?)',
                [date('Y-m-d H:i:s', $time), date('Y-m-d H:i:s', $time - $this->expire)]
            )
            ->asc('id')
            ->forUpdate()
            ->fetch();

        if ($job) {
            $job['payload'] = $this->unserialize($job['payload']);
            $job['payload']['attempts'] = $job['attempts'];
        }

        return $job;
    }

    /**
     * Mark the given job ID as reserved.
     *
     * @param array $job
     * @return array
     */
    protected function markJobAsReserved(array $job): array
    {
        ++$job['attempts'];
        $job['reserved_at'] = date('Y-m-d H:i:s', $this->getTime());

        QueryBuilder::table($this->table)
            ->where(['id' => $job['id']])
            ->update([
                'reserved_at' => $job['reserved_at'],
                'attempts' => $job['attempts'],
            ]);

        return $job;
    }
}
