<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Queue\BaseJob;

/**
 * A queue service based on Redis steam
 *
 * @mixin \RedisPropMixin
 * @mixin \TimePropMixin
 */
class RedisQueue extends BaseQueue
{
    /**
     * The name of the consumer group.
     *
     * @var string
     */
    protected $group = 'group';

    /**
     * The name of the consumer.
     *
     * @var string
     */
    protected $consumer = 'consumer';

    /**
     * The queue names that have been created
     *
     * @var array<string, bool>
     */
    protected $createdGroups = [];

    /**
     * {@inheritdoc}
     */
    public function pushRaw(array $payload, ?string $queue = null, $delay = 0): string
    {
        if ($delay > 0) {
            return $this->laterRaw($delay, $payload, $queue);
        } else {
            // * means auto generate id
            return $this->getRedis()->xAdd($this->getQueueKey($queue), '*', ['payload' => $this->serialize($payload)]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function pop(?string $name = null): ?BaseJob
    {
        $key = $this->getQueueKey($name);

        $this->createGroupOnce($key);
        $this->migrateAllExpiredJobs($key);

        // Get the first message without blocking from the queue
        // > means read from the beginning
        /** @phpstan-ignore-next-line Parameter #5 $block of method Redis::xReadGroup() expects int, null given. */
        $messages = $this->getRedis()->xReadGroup($this->group, $this->consumer, [$key => '>'], 1, null);

        if ($messages) {
            $id = key($messages[$key]);
            $message = current($messages[$key]);
            $payload = $this->unserialize($message['payload']);
            $payload['attempts'] = ($payload['attempts'] ?? 0) + 1;
            return $this->createJob($payload, $id, $this->getName($name));
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $id): bool
    {
        $this->getRedis()->xAck($this->getQueueKey(), $this->group, [$id]);
        return (bool) $this->getRedis()->xDel($this->getQueueKey(), [$id]);
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): void
    {
        $this->getRedis()->del($this->getQueueKey(), $this->getQueueKey() . ':delayed');
    }

    /**
     * Create a group to read messages
     *
     * Redis needs to create a group before reading messages
     *
     * @param string|null $queue
     * @return void
     * @internal
     */
    protected function createGroupOnce(?string $queue = null): void
    {
        if (!isset($this->createdGroups[$queue])) {
            $this->createdGroups[$queue] = true;
            // `0` means read from the beginning
            // `true` means create the stream if it is not exists
            $this->getRedis()->xGroup('CREATE', $queue, $this->group, '0', true);
        }
    }

    /**
     * Migrate all of the waiting jobs in the queue.
     *
     * @param string $queue
     * @return void
     */
    protected function migrateAllExpiredJobs(string $queue)
    {
        $luaScript = <<<'LUA'
local streamKey = KEYS[1]
local delayedKey = streamKey .. ':delayed'
local timestamp = ARGV[1]

local delayedMessages = redis.call('ZRANGEBYSCORE', delayedKey, '-inf', timestamp)

for _, delayedMessage in ipairs(delayedMessages) do
    redis.call('XADD', streamKey, '*', 'payload', delayedMessage)
    redis.call('ZREM', delayedKey, delayedMessage)
end
LUA;
        $this->getRedis()->eval($luaScript, [$queue, $this->time->timestamp()], 1);
    }

    /**
     * Push a raw payload onto the queue with a given delay.
     *
     * @param \DateTime|int $delay
     * @param array $payload
     * @param string|null $queue
     * @return string The id of the job
     */
    protected function laterRaw($delay, array $payload, ?string $queue = null): ?string
    {
        // Add id to make sure the job in the Redis sorted set is unique
        if (!isset($payload['id'])) {
            $payload['id'] = uniqid('', true);
        }

        $this->getRedis()->zAdd(
            $this->getQueueKey($queue) . ':delayed',
            $this->time->timestamp() + $delay,
            $this->serialize($payload)
        );
        return $payload['id'];
    }

    /**
     * Get the key or return the default key.
     *
     * @param string|null $name
     * @return string
     */
    protected function getQueueKey(?string $name = null): string
    {
        return 'queues:' . ($name ?: $this->name);
    }

    /**
     * Get the Redis instance.
     *
     * @return \Redis|\RedisCluster
     */
    protected function getRedis()
    {
        return $this->redis->getObject();
    }
}
