<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Ret;
use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * @internal
 */
final class RetTest extends BaseTestCase
{
    public function testSuc()
    {
        $ret = wei()->ret->suc();
        $this->assertSame([
            'message' => '操作成功',
            'code' => 1,
        ], $ret->toArray());
    }

    public function testSucWithMessage()
    {
        $ret = wei()->ret->suc('操作成功');
        $this->assertSame([
            'message' => '操作成功',
            'code' => 1,
        ], $ret->toArray());
    }

    public function testErr()
    {
        $ret = wei()->ret->err('Operation failed', 0);
        $this->assertSame([
            'message' => 'Operation failed',
            'code' => 0,
        ], $ret->toArray());
    }

    public function testSucWithArray()
    {
        $ret = wei()->ret->suc([
            'message' => 'Payment successful',
            'amount' => '10.00',
            'data' => [
                'key' => 'value',
            ],
        ]);
        $this->assertSame([
            'message' => 'Payment successful',
            'amount' => '10.00',
            'data' => [
                'key' => 'value',
            ],
            'code' => 1,
        ], $ret->toArray());
    }

    public function testSucWithFormat()
    {
        $ret = wei()->ret->suc(['me%sag%s', 'ss', 'e']);
        $this->assertSame('message', $ret['message']);
    }

    public function testErrWithFormat()
    {
        $ret = wei()->ret->err(['me%sage', 'ss'], 2);
        $this->assertSame([
            'message' => 'message',
            'code' => 2,
        ], $ret->toArray());
    }

    public function testIsErr()
    {
        $ret = wei()->ret->suc();
        $this->assertFalse($ret->isErr());

        $ret = wei()->ret->err('error');
        $this->assertTrue($ret->isErr());
    }

    public function testIsSuc()
    {
        $ret = wei()->ret->suc();
        $this->assertTrue($ret->isSuc());

        $ret = wei()->ret->err('error');
        $this->assertFalse($ret->isSuc());
    }

    public function testRetSuc()
    {
        $ret = Ret::suc('suc');
        $this->assertSame([
            'message' => 'suc',
            'code' => 1,
        ], $ret->toArray());
    }

    public function testRetErr()
    {
        $ret = Ret::err('err', 0);
        $this->assertSame([
            'message' => 'err',
            'code' => 0,
        ], $ret->toArray());
    }

    public function testCreateNewInstance()
    {
        $ret1 = Ret::suc();
        $ret2 = Ret::suc();

        $this->assertEquals($ret1, $ret2);
        $this->assertNotSame($ret1, $ret2);
    }

    public function testGetMetadata()
    {
        $ret = Ret::suc();

        $ret->setMetadata('key', 'value');
        $ret->setMetadata('key2', 'value2');

        $this->assertSame('value', $ret->getMetadata('key'));
        $this->assertSame(['key' => 'value', 'key2' => 'value2'], $ret->getMetadata());
    }

    public function testSetMetadata()
    {
        $ret = Ret::suc();

        $ret->setMetadata('key', 'value');
        $this->assertSame('value', $ret->getMetadata('key'));

        $ret->setMetadata(['key2' => 'value2']);
        $this->assertSame(['key2' => 'value2'], $ret->getMetadata());
    }

    public function testRemoveMetadata()
    {
        $ret = Ret::suc();

        $ret->setMetadata('key', 'value');
        $ret->setMetadata('key2', 'value2');

        $ret->removeMetadata('key');
        $this->assertSame(['key2' => 'value2'], $ret->getMetadata());

        $ret->removeMetadata();
        $this->assertSame([], $ret->getMetadata());
    }
}
