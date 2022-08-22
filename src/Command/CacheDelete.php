<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \CacheMixin
 */
class CacheDelete extends BaseCommand
{
    protected function handle()
    {
        $result = $this->cache->delete($this->getArgument('key'));
        if ($result) {
            $this->suc('Cache deleted.');
        } else {
            $this->err('Failed to delete the cache');
        }
    }

    protected function configure()
    {
        $this->setDescription('Remove a cache item')
            ->addArgument('key', InputArgument::REQUIRED, 'The name of cache item');
    }
}
