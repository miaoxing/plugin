<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Service\User;

/**
 * Add functions to the model to query the current logged in user's record
 *
 * @property-read string $userIdColumn The column contains user id
 */
trait MineTrait
{
    /**
     * @var self|null
     */
    protected static $mineCache;

    /**
     * Query: Filter by current user id
     *
     * @return $this
     * @svc
     */
    protected function mine(): self
    {
        return $this->where([$this->getUserIdColumn() => (string) User::id()]);
    }

    /**
     * Query: find or init the record by current user id
     *
     * @return $this
     * @svc
     */
    protected function findOrInitMine(): self
    {
        // NOTE: 未登录时，将 null 转为空字符串
        // 考虑对未登录做显式梳理
        return $this->findOrInitBy([$this->getUserIdColumn() => (string) User::id()]);
    }

    /**
     * Query: find or init and cache the record by current user id
     *
     * @return self
     * @svc
     */
    protected function findOrInitMineCached(): self
    {
        return static::$mineCache ?: static::$mineCache = $this->findOrInitMine();
    }

    /**
     * Returns the user id column
     *
     * @return string
     */
    protected function getUserIdColumn(): string
    {
        return $this->userIdColumn ?? $this->convertToPhpKey('user_id');
    }
}
