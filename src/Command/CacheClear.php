<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;
use Wei\BaseCache;

/**
 * @mixin \CacheMixin
 */
class CacheClear extends BaseCommand
{
    protected function configure()
    {
        $this->setDescription('Clear the cache')
            ->addArgument('driver', InputArgument::OPTIONAL, 'The service name of the cache');
    }

    protected function handle()
    {
        $driver = $this->getArgument('driver');

        if ($driver) {
            $cache = $this->wei->get($driver);
            if (!$cache instanceof BaseCache) {
                throw new \InvalidArgumentException(sprintf('Driver "%s" is not a instance of BaseCache', $driver));
            }
        } else {
            $cache = $this->cache;
        }

        $cache->clear();
        $this->suc('Cleared the cache!');
    }
}
