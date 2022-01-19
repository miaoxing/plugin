<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Config;
use Miaoxing\Plugin\Test\BaseTestCase;

class ConfigTest extends BaseTestCase
{
    public function testGetSetApp()
    {
        $value = Config::getApp('notfound:' . time());
        $this->assertNull($value);

        Config::setApp('test', __FUNCTION__);
        $this->assertSame(__FUNCTION__, Config::getApp('test'));
    }

    public function testDeleteApp()
    {
        Config::deleteApp('test');

        $this->assertNull(Config::getApp('test'));
    }

    public function testGetAppMultiple()
    {
        Config::setApp('test', 1);
        Config::setApp('test2', 2);

        $values = Config::getAppMultiple(['test', 'test2']);

        $this->assertSame([
            'test' => 1,
            'test2' => 2,
        ], $values);
    }

    public function testSetAppMultiple()
    {
        Config::setAppMultiple([
            'test' => __FUNCTION__ . '1',
            'test2' => __FUNCTION__ . '2',
        ]);

        $this->assertSame(__FUNCTION__ . '1', Config::getApp('test'));
        $this->assertSame(__FUNCTION__ . '2', Config::getApp('test2'));
    }

    public function testSetAppMultipleWithNewConfig()
    {
        Config::deleteApp('test');

        Config::setAppMultiple([
            'test' => __FUNCTION__ . '1',
            'test2' => __FUNCTION__ . '2',
        ]);

        $this->assertSame(__FUNCTION__ . '1', Config::getApp('test'));
        $this->assertSame(__FUNCTION__ . '2', Config::getApp('test2'));
    }
}
