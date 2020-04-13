<?php

namespace Miaoxing\Plugin\Model;

trait QueryBuilderCacheTrait
{
    /**
     * The default cache time
     *
     * @var int
     */
    protected $defaultCacheTime = 60;

    /**
     * The specified cache time
     *
     * @var int|false
     */
    protected $cacheTime = false;

    /**
     * @var string
     */
    protected $cacheKey = '';

    /**
     * The cache tags
     *
     * @var array
     */
    protected $cacheTags = array();

    /**
     * Clear cache that tagged with current table name
     *
     * @return $this
     */
    public function clearTagCache()
    {
        $this->tagCache($this->getCacheTags())->clear();
        return $this;
    }

    /**
     * Set or remove cache time for the query
     *
     * @param int|null|false $seconds
     * @return $this
     * @api
     */
    protected function cache($seconds = null)
    {
        if ($seconds === null) {
            $this->cacheTime = $this->defaultCacheTime;
        } elseif ($seconds === false) {
            $this->cacheTime = false;
        } else {
            $this->cacheTime = (int) $seconds;
        }
        return $this;
    }

    /**
     * Set or remove cache tags
     *
     * @param array|null|false $tags
     * @return $this
     */
    public function tags($tags = null)
    {
        $this->cacheTags = $tags === false ? false : $tags;
        return $this;
    }

    /**
     * Set cache key
     *
     * @param string $cacheKey
     * @return $this
     */
    public function setCacheKey($cacheKey)
    {
        $this->cacheKey = $cacheKey;
        return $this;
    }

    /**
     * Generate cache key form query and params
     *
     * @return string
     */
    public function getCacheKey()
    {
        return $this->cacheKey ?:
            md5($this->db->getDbname() . $this->getSql() . serialize($this->params) . serialize($this->paramTypes));
    }

    /**
     * @return array
     */
    protected function getCacheTags()
    {
        $tags[] = $this->getTable();
        foreach ($this->sqlParts['join'] as $join) {
            $tags[] = $join['table'];
        }
        return $tags;
    }
}
