<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\User;
use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * 用户服务
 */
class UserTest extends BaseTestCase
{
    /**
     * 测试获取昵称
     */
    public function testGetNickName()
    {
        $user = wei()->user();
        $user['isValid'] = false;
        $this->assertEquals('', $user->getNickName());

        $user['isValid'] = true;
        $this->assertEquals('', $user->getNickName());

        $user['name'] = 'name';
        $this->assertEquals('name', $user->getNickName());

        $user['username'] = 'username';
        $this->assertEquals('username', $user->getNickName());

        $user['nickName'] = 'nickName';
        $this->assertEquals('nickName', $user->getNickName());
    }

    public function testIsAdmin()
    {
        $user = wei()->user();
        $this->assertFalse($user->isAdmin());

        $user['admin'] = true;
        $this->assertTrue($user->isAdmin());
    }

    public function testGetHeadImg()
    {
        $user = wei()->user();
        $this->assertEquals(wei()->user->getDefaultHeadImg(), $user->getHeadImg());

        $user['headImg'] = 'test.jpg';
        $this->assertEquals('test.jpg', $user->getHeadImg());
    }

    public function testStatus()
    {
        $user = wei()->user();

        $user->setStatus(1, true);
        $user->setStatus(2, false);
        $user->setStatus(3, true);
        $user->setStatus(4, false);
        $user->save();

        $user->reload();
        $this->assertTrue($user->isStatus(1));
        $this->assertFalse($user->isStatus(2));
        $this->assertTrue($user->isStatus(3));
        $this->assertFalse($user->isStatus(4));
    }

    public function testWithStatus()
    {
        $user = wei()->user()->withStatus(User::STATUS_MOBILE_VERIFIED);

        $this->assertContains('status & 1 = 1', $user->getSqlPart('where'));
    }

    public function testWithoutStatus()
    {
        $user = wei()->user()->withoutStatus(User::STATUS_MOBILE_VERIFIED);

        $this->assertContains('status & 1 = 0', $user->getSqlPart('where'));
    }

    /**
     * 检查手机号码能否绑定
     */
    public function testCheckMobile()
    {
        $this->step('清空原测试数据');
        $testMobile = '12800138000';
        wei()->user()->delete(['mobile' => $testMobile]);

        $this->step('未存在指定手机号码的用户,可以绑定');
        $testUser = wei()->user();
        $ret = $testUser->checkMobile($testMobile);
        $this->assertRetSuc($ret, '手机号码可以绑定');

        $this->step('存在指定手机号码的用户,但是未认证,可以绑定');
        $user = wei()->user()->save(['mobile' => $testMobile]);
        $ret = $testUser->checkMobile($testMobile);
        $this->assertRetSuc($ret, '手机号码可以绑定');

        $this->step('存在认证手机号码的用户,不能绑定');
        $user->setStatus(User::STATUS_MOBILE_VERIFIED, true)->save();
        $ret = $testUser->checkMobile($testMobile);
        $this->assertRetErr($ret, -1, '已存在认证该手机号码的用户');
    }
}
