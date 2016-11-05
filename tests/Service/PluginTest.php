<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

class PluginTest extends BaseTestCase
{
    public function testGetById()
    {
        $plugin = wei()->plugin->getById('plugin');

        $this->assertInstanceOf('Miaoxing\Plugin\Plugin', $plugin);
    }

    public function testGetByIdButPluginNotExists()
    {
        $plugin = wei()->plugin->getById('not-exists');

        $this->assertFalse($plugin);
    }

    public function testGetOneById()
    {
        $plugin = wei()->plugin->getOneById('plugin');

        $this->assertInstanceOf('miaoxing\plugin\Plugin', $plugin);
    }

    public function testGetOneByIdButPluginNotExists()
    {
        $this->setExpectedException('Exception', 'Plugin "not-exists" not found', 404);

        wei()->plugin->getOneById('not-exists');
    }
}
