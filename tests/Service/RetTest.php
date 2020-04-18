<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Ret;
use Miaoxing\Plugin\Test\BaseTestCase;

class RetTest extends BaseTestCase
{
    public function testSuc()
    {
        $ret = wei()->ret->suc();
        $this->assertSame([
            'message' => 'Operation successful',
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
        $ret = wei()->ret->err('Operation failed');
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
        $ret = Ret::suc('c');
        $this->assertSame('x', 'x');
    }
}
