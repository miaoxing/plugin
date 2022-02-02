<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Config;
use Miaoxing\Plugin\Service\ConfigModel;
use Miaoxing\Plugin\Service\GlobalConfigModel;
use Miaoxing\Plugin\Test\BaseTestCase;
use Wei\NullCache;

class ConfigTest extends BaseTestCase
{
    public function testGetSetApp()
    {
        $value = Config::getApp('notFound:' . time());
        $this->assertNull($value);

        Config::setApp('test', __FUNCTION__);
        $this->assertSame(__FUNCTION__, Config::getApp('test'));
    }

    public function testGetAppWithDefault()
    {
        $key = 'notFound' . time();

        $value = Config::getApp($key, 'default');
        $this->assertSame($value, 'default');

        $value = Config::getApp($key, 'default2');
        $this->assertSame($value, 'default2');
    }

    public function testGetAppFromCache()
    {
        $key = 'test:' . microtime(true);

        $config = $this->getServiceMock(Config::class, [
            'getMultipleFromDb',
        ]);

        $config->expects($this->once())
            ->method('getMultipleFromDb')
            ->with(ConfigModel::class, [$key])
            ->willReturn([[], [$key]]);

        $value = $config->getApp($key, 'default');
        $this->assertSame('default', $value);

        $value = $config->getApp($key, 'default2');
        $this->assertSame('default2', $value);
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

    public function testGetAppMultipleWithDefault()
    {
        $key1 = 'test' . time();
        $key2 = 'test2' . time();

        $values = Config::getAppMultiple([$key1, $key2], [
            $key1 => 'test',
            $key2 => 'test2',
        ]);

        $this->assertSame([
            $key1 => 'test',
            $key2 => 'test2',
        ], $values);

        $values = Config::getAppMultiple([$key1, $key2], [
            $key1 => 'test2',
            $key2 => 'test3',
        ]);

        $this->assertSame([
            $key1 => 'test2',
            $key2 => 'test3',
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
        ksort($configs);
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

    public function testGetGlobalWithDefault()
    {
        $key = 'notFound' . time();

        $value = Config::getGlobal($key, 'default');
        $this->assertSame($value, 'default');

        $value = Config::getGlobal($key, 'default2');
        $this->assertSame($value, 'default2');
    }

    public function testSetGlobalWithPreload()
    {
        Config::deleteGlobal('test');

        Config::setGlobal('test', 'value', ['preload' => true]);

        $config = GlobalConfigModel::findBy('name', 'test');
        $this->assertTrue($config->preload);
    }

    public function testDeleteGlobal()
    {
        Config::deleteGlobal('test');

        $this->assertNull(Config::getGlobal('test'));
    }

    public function testGetGlobalMultiple()
    {
        Config::setGlobal('test', 1);
        Config::setGlobal('test2', 2);

        $values = Config::getGlobalMultiple(['test', 'test2']);

        $this->assertSame([
            'test' => 1,
            'test2' => 2,
        ], $values);
    }

    public function testGetGlobalMultipleWithDefault()
    {
        $key1 = 'test' . time();
        $key2 = 'test2' . time();

        $values = Config::getGlobalMultiple([$key1, $key2], [
            $key1 => 'test',
            $key2 => 'test2',
        ]);

        $this->assertSame([
            $key1 => 'test',
            $key2 => 'test2',
        ], $values);

        $values = Config::getGlobalMultiple([$key1, $key2], [
            $key1 => 'test2',
            $key2 => 'test3',
        ]);

        $this->assertSame([
            $key1 => 'test2',
            $key2 => 'test3',
        ], $values);
    }

    public function testSetGlobalMultiple()
    {
        Config::setAppMultiple([
            'test' => __FUNCTION__ . '1',
            'test2' => __FUNCTION__ . '2',
        ]);

        $this->assertSame(__FUNCTION__ . '1', Config::getApp('test'));
        $this->assertSame(__FUNCTION__ . '2', Config::getApp('test2'));
    }

    public function testSetGlobalMultipleWithNewConfig()
    {
        Config::deleteGlobal('test');

        Config::setGlobalMultiple([
            'test' => __FUNCTION__ . '1',
            'test2' => __FUNCTION__ . '2',
        ]);

        $this->assertSame(__FUNCTION__ . '1', Config::getGlobal('test'));
        $this->assertSame(__FUNCTION__ . '2', Config::getGlobal('test2'));
    }

    public function testGetGlobalSection()
    {
        Config::setGlobalMultiple([
            'testSms.appKey' => '123',
            'testSms.appSecret' => '456',
        ]);

        $configs = Config::getGlobalSection('testSms');
        $this->assertSame([
            'appKey' => '123',
            'appSecret' => '456',
        ], $configs);
    }

    public function testGetSet()
    {
        $value = Config::get('notFound:' . time());
        $this->assertNull($value);

        Config::set('test', __FUNCTION__);
        $this->assertSame(__FUNCTION__, Config::get('test'));
    }

    public function testGetWithDefault()
    {
        $key = 'test:' . microtime(true);

        $value = Config::get($key, 'default');
        $this->assertSame('default', $value);

        $value = Config::get($key, 'default2');
        $this->assertSame($value, 'default2');
    }

    public function testGetMultiple()
    {
        Config::set('test', 1);
        Config::set('test2', 2);

        $values = Config::getMultiple(['test', 'test2']);

        $this->assertSame([
            'test' => 1,
            'test2' => 2,
        ], $values);
    }

    public function testGetMultipleWithDefault()
    {
        $key1 = 'test' . time();
        $key2 = 'test2' . time();

        $values = Config::getMultiple([$key1, $key2], [
            $key1 => 'test',
            $key2 => 'test2',
        ]);

        $this->assertSame([
            $key1 => 'test',
            $key2 => 'test2',
        ], $values);

        $values = Config::getMultiple([$key1, $key2], [
            $key1 => 'test2',
            $key2 => 'test3',
        ]);

        $this->assertSame([
            $key1 => 'test2',
            $key2 => 'test3',
        ], $values);
    }

    public function testGetMultipleFallbackToGlobal()
    {
        Config::setApp('test', 'value');
        Config::deleteApp('test2');
        Config::setGlobal('test2', 2);

        $values = Config::getMultiple(['test', 'test2']);

        // Get from app
        $this->assertSame('value', $values['test']);

        // Get from global
        $this->assertSame(2, $values['test2']);
    }

    public function testGetMultipleFallbackToContainerConfig()
    {
        Config::setApp('test', 'value');

        Config::deleteApp('test2');
        Config::setGlobal('test2', 2);

        Config::deleteGlobal('test3');
        Config::deleteApp('test3');
        $this->wei->setConfig('test3', 3);

        $values = Config::getMultiple(['test', 'test2', 'test3']);

        // Get from app
        $this->assertSame('value', $values['test']);

        // Get from global
        $this->assertSame(2, $values['test2']);

        // Get from global
        $this->assertSame(3, $values['test3']);
    }

    public function testSetMultiple()
    {
        Config::setMultiple([
            'test' => __FUNCTION__ . '1',
            'test2' => __FUNCTION__ . '2',
        ]);

        $this->assertSame(__FUNCTION__ . '1', Config::get('test'));
        $this->assertSame(__FUNCTION__ . '2', Config::get('test2'));
    }

    public function testSetMultipleWithEmptyArray()
    {
        $config = Config::setMultiple([]);
        $this->assertInstanceOf(Config::class, $config);
    }

    public function testGetSection()
    {
        Config::setAppMultiple([
            'testSms.appKey' => '123',
            'testSms.appSecret' => '456',
        ]);

        $configs = Config::getSection('testSms');
        $this->assertSame([
            'appKey' => '123',
            'appSecret' => '456',
        ], $configs);
    }

    public function testGetSectionFallbackToGlobal()
    {
        Config::setGlobalMultiple([
            'testSms.appKey' => 'globalKey',
        ]);

        Config::deleteApp('testSms.appKey');
        Config::setAppMultiple([
            'testSms.appSecret' => '456',
        ]);

        $configs = Config::getSection('testSms');
        $this->assertSame([
            'appKey' => 'globalKey',
            'appSecret' => '456',
        ], $configs);
    }

    public function testGetSectionFallbackToContainerConfig()
    {
        $this->wei->setConfig('testSms.limit', 1);

        Config::deleteGlobal('testSms.limit');
        Config::setGlobal('testSms.appKey', 'globalKey');

        Config::deleteApp('testSms.appKey');
        Config::setApp('testSms.appSecret', '456');

        $configs = Config::getSection('testSms');
        $this->assertSame([
            'limit' => 1,
            'appKey' => 'globalKey',
            'appSecret' => '456',
        ], $configs);

        $this->wei->removeConfig('testSms.limit');
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

    public function testGetFallbackToContainerConfig()
    {
        $this->wei->setConfig(__FUNCTION__, 'value');

        $value = Config::get(__FUNCTION__);

        $this->assertSame('value', $value);
    }

    /**
     * @dataProvider providerForTypes
     * @param mixed $value
     */
    public function testGetTypes(string $key, $value)
    {
        Config::instance()->setOption('cache', NullCache::instance());
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
            ['test9', new \ArrayObject(['e' => 'f'])],
        ];
    }

    public function testScope()
    {
        $time = time();
        Config::setGlobal('test', $time);
        $this->assertNotSame($time, Config::getApp('test'));

        Config::setApp('test', $time + 1);
        $this->assertNotSame($time + 1, Config::getGlobal('test'));
    }

    public function testCreateService()
    {
        Config::setApp('config.localFile', 'test.php');

        $config = Config::createService('config');
        $this->assertInstanceOf(Config::class, $config);

        $config2 = Config::createService('config');
        $this->assertEquals($config, $config2);
        $this->assertNotSame($config, $config2);

        $this->assertSame('test.php', $config->getOption('localFile'));
    }

    public function testGetService()
    {
        $ret = Config::getService('config');
        $this->assertInstanceOf(Config::class, $ret);

        $ret2 = Config::getService('config');
        $this->assertSame($ret, $ret2);
    }

    public function testGetServiceByClass()
    {
        $config = Config::getService(Config::class);
        $this->assertInstanceOf(Config::class, $config);

        $config2 = Config::getService(\Wei\Config::class);
        $this->assertInstanceOf(Config::class, $config2);

        $this->assertSame($config2, $config);
    }

    public function testUpdateLocal()
    {
        $options = [
            'null' => null,
            'string' => 'string',
            'int' => 1,
            'float' => 1.1,
            'true' => true,
            'false' => false,
            'empty-array' => [],
            'array' => [
                'a' => 'b',
            ],
        ];

        $file = 'storage/test.php';
        if (is_file($file)) {
            unlink($file);
        }

        /** @var Config $config */
        $config = $this->wei->newInstance('config', [
            'localFile' => $file,
        ]);

        $config->updateLocal([
            'test' => $options,
        ]);
        $this->assertArrayContains($options, $this->wei->getConfig('test'));

        $config->updateLocal([
            'test' => [
                'string' => 'string2',
            ],
        ]);

        $this->assertSame('string2', $this->wei->getConfig('test.string'));

        unlink($file);
    }

    public function testPublishPreload()
    {
        $this->wei->removeConfig('test.now');

        $now = time();
        Config::setGlobal('test.now', $now, ['preload' => true]);

        $testNow = $this->wei->getConfig('test.now');
        $this->assertNotSame($now, $testNow);

        Config::publishPreload();

        $testNow = $this->wei->getConfig('test.now');
        $this->assertSame($now, $testNow);
    }

    public function testPreloadGlobal()
    {
        $key = Config::getPreloadVersionKey();

        Config::deleteGlobal($key);

        Config::preloadGlobal();

        $version = Config::getGlobal($key);

        $this->assertNotNull($version);
    }
}
