<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;
use Wei\Validate;

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

    public function testMessage()
    {
        $ret = wei()->v()
            ->key('name', '名称')->message('required', '请填写%name%')
            ->check([]);

        $this->assertRetErr($ret, -1, '请填写名称');
    }

    public function testMessageWithoutRule()
    {
        $ret = wei()->v()
            ->key('name', '名称')->required()->message('请填写%name%')
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

    public function testMobileCn()
    {
        $ret = wei()->v()
            ->key('mobile', '手机')->mobileCn()
            ->check([
                'mobile' => '123',
            ]);
        $this->assertRetErr($ret, -1, '手机必须是11位长度的数字,以13,14,15,17或18开头');
    }

    public function testValidate()
    {
        $validator = wei()->v()
            ->key('mobile')->mobileCn()
            ->validate(['mobile' => '123']);

        $this->assertInstanceOf(Validate::class, $validator);
        $this->assertFalse($validator->isValid());
    }

    public function testIsValid()
    {
        $result = wei()->v()
            ->key('mobile')->mobileCn()
            ->isValid(['mobile' => '123']);

        $this->assertFalse($result);
    }
}
