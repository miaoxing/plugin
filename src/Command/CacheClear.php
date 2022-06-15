<?php

namespace Miaoxing\Plugin\Command;

/**
 * @mixin \CacheMixin
 */
class CacheClear extends BaseCommand
{
    protected function handle()
    {
        $this->cache->clear();
        $this->suc('Cleared the cache!');
    }
}
