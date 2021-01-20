<?php

namespace Miaoxing\Plugin\Model;

/**
 * Add cache functions to the model
 */
trait CacheTrait
{
    /**
     * Remove the model cache
     *
     * @return $this
     */
    public function removeModelCache(): self
    {
        if ($this->getColumnValue('id')) {
            $this->cache->remove($this->getModelCacheKey());
        }
        return $this;
    }

    /**
     * Return the model cache key
     *
     * @param int|null $id
     * @return string
     */
    public function getModelCacheKey($id = null): string
    {
        return $this->getDb()->getDbname() . ':' . $this->getTable() . ':' . ($id ?: $this->getColumnValue('id'));
    }
}
