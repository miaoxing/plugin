<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Config;
use Miaoxing\Plugin\Test\BaseTestCase;
use Wei\Cache;

class ConfigTest extends BaseTestCase
{
    public function testGetFallbackToWeiConfig()
    {
        $this->wei->setConfig(__FUNCTION__, 'value');

        $value = Config::get(__FUNCTION__);

        $this->assertSame('value', $value);
    }

    /**
     * @dataProvider providerForTypes
     */
    public function testGetTypes($key, $value)
    {
        $cache = $this->getModelServiceMock(Cache::class, [
            'setMultiple',
            'get'
        ]);

        $cache->expects($this->exactly(2))
            ->method('setMultiple')
            ->willReturn(false);

        $cache->expects($this->once())
            ->method('get')
            ->willReturn(null);

        Config::instance()->setOption('cache', $cache);
        Config::setGlobal($key, $value);

        $result = Config::getGlobal($key);

        if (is_scalar($value)) {
            $this->assertSame($result, $value);
        } else {
            $this->assertEquals($result, $value);
        }
    }

    public function providerForTypes(): array
    {
        return [
            ['test1', null],
            ['test2', 'string'],
            ['test3', 1],
            ['test4', 1.1],
            ['test5', true],
            ['test6', false],
            ['test7', []],
            ['test8', ['a' => 'b']],
            ['test8', (object) ['c' => 'd']],
            ['test9', new \ArrayObject(['e' => 'f'])]
        ];
    }

    public function testGetSetApp()
    {
        $value = Config::getApp('notFound:' . time());
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

        // app cache wont be cache to null
        $this->assertSame(1, Config::get('test'));
    }
}
