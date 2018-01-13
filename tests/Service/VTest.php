<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

class VTest extends BaseTestCase
{
    public function testCheckFail()
    {
        $ret = wei()->v()
            ->key('question', '问题')
            ->check([]);

        $this->assertRetErr($ret, -1, '问题不能为空');
    }

    public function testCheckPass()
    {
        $ret = wei()->v()
            ->key('question', '问题')
            ->check([
                'question' => '问题',
            ]);

        $this->assertRetSuc($ret);
    }

    public function testCustomMessage()
    {
        $ret = wei()->v()
            ->key('name', '名称')->message('required', '请填写%name%')
            ->check([]);

        $this->assertRetErr($ret, -1, '请填写名称');
    }

    public function testCallback()
    {
        $ret = wei()->v()
            ->key('name')->callback(function ($name) {
                return $name !== 'twin';
            })
            ->check(['name' => 'twin']);
        $this->assertRetErr($ret, -1, '该项不合法');

        $ret = wei()->v()
            ->key('name')->callback(function ($name) {
                return $name !== 'twin';
            })
            ->check(['name' => 'hi']);
        $this->assertRetSuc($ret);
    }
}
