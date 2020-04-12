<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

class PluginTest extends BaseTestCase
{
    public function testGetById()
    {
        $plugin = wei()->plugin->getById('plugin');

        $this->assertInstanceOf(\Miaoxing\Plugin\BasePlugin::class, $plugin);
    }

    public function testGetByIdButPluginNotExists()
    {
        $plugin = wei()->plugin->getById('not-exists');

        $this->assertFalse($plugin);
    }

    public function testGetOneById()
    {
        $plugin = wei()->plugin->getOneById('plugin');

        $this->assertInstanceOf(\Miaoxing\Plugin\BasePlugin::class, $plugin);
    }

    public function testGetOneByIdButPluginNotExists()
    {
        $this->expectExceptionObject(new \Exception('Plugin "not-exists" not found'));

        wei()->plugin->getOneById('not-exists');
    }
}
