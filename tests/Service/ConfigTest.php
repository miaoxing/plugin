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

    public function testGetAppSection()
    {
        Config::setAppMultiple([
            'testSms.appKey' => '123',
            'testSms.appSecret' => '456',
        ]);

        $configs = Config::getAppSection('testSms');
        $this->assertSame([
            'appKey' => '123',
            'appSecret' => '456',
        ], $configs);
    }

    public function testGetSetGlobal()
    {
        $value = Config::getGlobal('notfound:' . time());
        $this->assertNull($value);

        Config::setGlobal('test', __FUNCTION__);
        $this->assertSame(__FUNCTION__, Config::getGlobal('test'));

        Config::get('test');
    }

    public function testDeleteGlobal()
    {
        Config::deleteGlobal('test');

        $this->assertNull(Config::getGlobal('test'));
    }

    public function testScope()
    {
        $time = time();
        Config::setGlobal('test', $time);
        $this->assertNotSame($time, Config::getApp('test'));

        Config::setApp('test', $time + 1);
        $this->assertNotSame($time + 1, Config::getGlobal('test'));
    }

    public function testGetFromApp()
    {
        Config::setApp('test', 1);
        $this->assertSame(1, Config::get('test'));
    }

    public function testGetFallbackToGlobal()
    {
        Config::deleteApp('test');
        Config::setGlobal('test', 1);

        $this->assertSame(1, Config::get('test'));
    }
}
