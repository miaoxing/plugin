<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Request;
use Miaoxing\Plugin\Test\BaseTestCase;

class RequestTest extends BaseTestCase
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \Wei\Request
     */
    protected $object;

    public function setUp()
    {
        parent::setUp();

        $this->request = wei()->request;
        $this->object = wei()->request;
    }

    protected function initExtraKey()
    {
        // 移除数据避免干扰
        unset($this->request['test']);
        $this->assertArrayNotHasKey('test', $this->request);

        $extraKeys = $this->request->getOption('extraKeys');
        $this->assertArrayNotHasKey('test', $extraKeys);

        // 触发了 &offsetGet 产生了 test 键名
        $this->request['test'];
    }

    public function testExtraKeyInit()
    {
        $this->initExtraKey();

        $extraKeys = $this->request->getOption('extraKeys');
        $this->assertArrayHasKey('test', $extraKeys);

        $this->assertArrayNotHasKey('test', $this->request);
    }

    public function testExtraKeyToArray()
    {
        $this->initExtraKey();

        $array = $this->request->toArray();

        $this->assertArrayNotHasKey('test', $array, 'toArray不会带上额外键名');
    }

    public function testExtraKeyForEach()
    {
        $this->initExtraKey();

        $array = [];
        foreach ($this->request as $key => $value) {
            $array[$key] = $value;
        }

        $this->assertArrayNotHasKey('test', $array, 'forEach不会带上额外键名');
    }

    public function testExtraKeySet()
    {
        $this->initExtraKey();

        // 触发的是offsetSet,因此不会生成extraKey
        $this->request['test'] = 'value';

        $this->assertArrayNotHasKey('test', $this->request->getOption('extraKeys'));

        $this->assertEquals('value', $this->request->toArray()['test']);
    }

    public function testExtraKeySetMultiLevel()
    {
        $this->initExtraKey();

        // 触发的是offsetGet,因此会生成extraKey
        $this->request['test']['level2'] = 'value';

        $this->assertArrayHasKey('test', $this->request->getOption('extraKeys'));

        $this->assertEquals('value', $this->request['test']['level2']);

        $this->assertEquals('value', $this->request->toArray()['test']['level2']);
    }

    public function testExtraKeyCount()
    {
        $count = count($this->request);

        $this->initExtraKey();

        $this->assertEquals($count, count($this->request));
    }

    public function testExtraKeyUnset()
    {
        $this->initExtraKey();

        $this->request['test']['level2'] = 'value';

        unset($this->request['test']);

        $this->assertArrayNotHasKey('test', $this->request->getOption('extraKeys'));
        $this->assertArrayNotHasKey('test', $this->request->toArray());
    }

    public function testExtraKeySetNull()
    {
        $this->initExtraKey();

        // 主动设置了,不会在extraKey里面
        $this->request['test'] = null;

        $this->assertArrayNotHasKey('test', $this->request->getOption('extraKeys'));

        $this->assertArrayHasKey('test', $this->request->toArray());
    }

    /**
     * NOTE 以下为额外的\Wei\Request的测试,待合并
     */
    public function testInvoke()
    {
        $wei = $this->object;

        $name = $wei->request('name');
        $source = isset($_REQUEST['name']) ? $_REQUEST['name'] : null;

        $this->assertEquals($name, $source);

        $default = 'default';
        $name2 = $wei->request('name', $default);
        $source = isset($_REQUEST['name']) ? $_REQUEST['name'] : $default;

        $this->assertEquals($name2, $default);
    }

    public function testSet()
    {
        $wei = $this->object;

        $wei->set('key', 'value');

        $this->assertEquals('value', $wei->request('key'), 'string param');

        $wei->fromArray([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $this->assertEquals('value2', $wei->request('key2'), 'array param');
    }

    public function testRemove()
    {
        $wei = $this->object;

        $wei->set('remove', 'just a moment');

        $this->assertEquals('just a moment', $wei->request('remove'));

        $wei->remove('remove');

        $this->assertNull($wei->request->get('remove'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidParameterReference()
    {
        $this->request->getParameterReference('exception');
    }

    /**
     * @link https://github.com/twinh/wei/issues/54
     */
    public function testErrorParameterTypeWhenFromGlobalIsFalse()
    {
        $request = new \Wei\Request([
            'fromGlobal' => false,
        ]);

        foreach (['get', 'cookie', 'server', 'file'] as $option) {
            $this->assertInternalType('array', $request->getParameterReference($option));
        }
    }

    public function testInvoker()
    {
        $request = $this->object;

        $request->fromArray([
            'string' => 'value',
            1 => 2,
        ]);

        $this->assertEquals('value', $request('string'));

        $this->assertEquals('custom', $request('no this key', 'custom'));
    }

    public function testCount()
    {
        $wei = $this->object;

        $wei->fromArray(range(1, 10));

        $this->assertCount(10, $wei);
    }

    public function testFromArray()
    {
        $wei = $this->object;

        $wei['key2'] = 'value2';

        $wei->fromArray([
            'key1' => 'value1',
            'key2' => 'value changed',
        ]);

        $this->assertEquals('value1', $wei['key1']);

        $this->assertEquals('value changed', $wei['key2']);
    }

    public function testToArray()
    {
        $wei = $this->object;

        $wei->fromArray([
            'key' => 'other value',
        ]);

        $arr = $wei->toArray();

        $this->assertContains('other value', $arr);
    }

    public function testOffsetExists()
    {
        $wei = $this->object;

        $wei['key'] = 'value';

        $this->assertTrue(isset($wei['key']));
    }

    public function testOffsetGet()
    {
        $wei = $this->object;

        $wei['key'] = 'value1';

        $this->assertEquals('value1', $wei['key']);
    }

    public function testOffsetUnset()
    {
        $wei = $this->object;

        unset($wei['key']);

        $this->assertNull($wei['key']);
    }

    public function createParameterObject($type, $class)
    {
        // create request wei from custom parameter
        $request = new \Wei\Request([
            'wei' => $this->wei,
            'fromGlobal' => false,
            $type => [
                'key' => 'value',
                'key2' => 'value2',
                'int' => '5',
                'array' => [
                    'item' => 'value',
                ],
            ],
        ]);

        return $request;
    }

    public function testGetter()
    {
        $parameters = [
            'data' => 'request',
        ];

        foreach ($parameters as $type => $class) {
            $parameter = $this->createParameterObject($type, $class);

            $this->assertEquals('value', $parameter->get('key'));

            $this->assertEquals(5, $parameter->getInt('int'));

            $this->assertEquals('', $parameter->get('notFound'));

            // int => 5, not in specified array
            $this->assertEquals('firstValue', $parameter->getInArray('int', [
                'firstKey' => 'firstValue',
                'secondKey' => 'secondValue',
            ]));

            // int => 5
            $this->assertEquals(6, $parameter->getInt('int', 6));

            $this->assertEquals(6, $parameter->getInt('int', 6, 10));

            $this->assertEquals(4, $parameter->getInt('int', 1, 4));
        }
    }

    public function testOverwriteAjax()
    {
        $request = new Request([
            'wei' => $this->wei,
            'data' => [],
        ]);
        $this->assertFalse($request->isAjax());

        $request = new Request([
            'wei' => $this->wei,
            'data' => [
                '_ajax' => true,
            ],
        ]);
        $this->assertFalse($request->isAjax());
    }

    public function testOverwriteMethod()
    {
        $request = new Request([
            'wei' => $this->wei,
            'fromGlobal' => false,
            'data' => [
                '_method' => 'PUT',
            ],
        ]);
        $this->assertTrue($request->isMethod('PUT'));
    }

    public function testArrayAccess()
    {
        $this->assertArrayBehaviour([]);
        $this->assertArrayBehaviour($this->request);
    }

    public function assertArrayBehaviour($arr)
    {
        // Behaviour 1
        $arr['a']['b'] = true;
        $this->assertTrue($arr['a']['b'], 'Assign multi level array directly');

        // Behaviour 2
        $prev = error_reporting(-1);
        $hasException = false;
        try {
            $arr['b'];
        } catch (\Exception $e) {
            if ($e->getMessage() == 'Undefined index: b') {
                $hasException = true;
            } else {
                throw $e;
            }
        }
        error_reporting($prev);
        if (gettype($arr) == 'array') {
            $this->assertTrue($hasException, 'Access array\'s undefined index would cause error');
        } else {
            $this->assertFalse($hasException, 'Access object\'s undefined index won\'t cause error');
        }

        $this->assertFalse(isset($arr['b']), 'Access undefined index won\'t create key');

        // Behaviour 3
        $arr['c'] = [];
        $arr['c']['d'] = 'e';
        $this->assertEquals('e', $arr['c']['d'], 'Allow to create next level array');

        // Behaviour 4
        unset($arr['d']);
        $this->assertFalse(isset($arr['d']));

        $arr['d'] = null;
        $this->assertFalse(isset($arr['d']), 'Call isset returns false when value is null');

        // Behaviour 5
        if (method_exists($arr, 'toArray')) {
            $origArr = $arr->toArray();
        } else {
            $origArr = $arr;
        }
        $this->assertArrayHasKey('d', $origArr, 'Call array_key_exists returns true even if value is null');
    }
}
