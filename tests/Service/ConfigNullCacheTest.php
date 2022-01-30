<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Config;
use Wei\NullCache;

class ConfigNullCacheTest extends ConfigTest
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::instance()->setOption('cache', NullCache::instance());
    }
}
