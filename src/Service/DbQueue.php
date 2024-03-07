<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;
use Wei\QueryBuilder;

/**
 * @mixin \DbPropMixin
 * @mixin \TimePropMixin
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
     * The number of seconds before a reserved job will be available.
     *
     * @var int
     */
    protected $retryAfter = 60;

    /**
     * {@inheritdoc}
     */
    public function pushRaw(array $payload, ?string $queue = null, $delay = 0): string
    {
        $createdAt = $availableAt = $this->time->timestamp();
        if ($delay > 0) {
            $availableAt += $delay;
        }

        $this->db->insert($this->table, [
            'queue' => $this->getName($queue),
            'payload' => $this->serialize($payload),
            'attempts' => $payload['attempts'] ?? 0,
            'created_at' => date('Y-m-d H:i:s', $createdAt),
            'available_at' => date('Y-m-d H:i:s', $availableAt),
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function pop(?string $name = null): ?BaseJob
    {
        $name = $this->getName($name);

        $pdo = $this->db->getPdo();
        $pdo->beginTransaction();

        if ($job = $this->getNextAvailableJob($name)) {
            $job = $this->markJobAsReserved($job);

            $pdo->commit();

            return $this->createJob($job['payload'], $job['id'], $name);
        }

        $pdo->commit();
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $id): bool
    {
        return (bool) $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->db->delete($this->table, []);
    }

    /**
     * @return int
     */
    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }

    /**
     * @param int $retryAfter
     * @return DbQueue
     */
    public function setRetryAfter(int $retryAfter): self
    {
        $this->retryAfter = $retryAfter;
        return $this;
    }

    /**
     * Get the next available job for the queue.
     *
     * @param string|null $queue
     * @return array|null
     */
    protected function getNextAvailableJob(?string $queue): ?array
    {
        $time = $this->time->timestamp();
        $job = QueryBuilder::table($this->table)
            ->where(['queue' => $this->getName($queue)])
            ->whereRaw(
            // available or reserved but expired
                '(reserved_at IS NULL AND available_at <= ?) OR (reserved_at <= ?)',
                [date('Y-m-d H:i:s', $time), date('Y-m-d H:i:s', $time - $this->retryAfter)]
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
        ++$job['payload']['attempts'];
        $job['reserved_at'] = date('Y-m-d H:i:s', $this->time->timestamp());

        QueryBuilder::table($this->table)
            ->where(['id' => $job['id']])
            ->update([
                'reserved_at' => $job['reserved_at'],
                'attempts' => $job['payload']['attempts'],
            ]);

        return $job;
    }
}
