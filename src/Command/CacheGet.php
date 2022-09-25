<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \CacheMixin
 */
class CacheGet extends BaseCommand
{
    protected function handle()
    {
        $result = $this->cache->get($this->getArgument('key'));
        var_export($result);
    }

    protected function configure()
    {
        $this->setDescription('Retrieve a cache item')
            ->addArgument('key', InputArgument::REQUIRED, 'The name of cache item');
    }
}
