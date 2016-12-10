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
        $this->assertEquals('', $user->getNickName());

        $user['isValid'] = true;
        $this->assertEquals('游客', $user->getNickName());

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
        $this->assertEquals('/assets/images/head/default-light.jpg', $user->getHeadImg());

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

    public function testUpdateGroup()
    {
        $user = wei()->user();
        $user->updateGroup(1);

        $this->assertEquals(1, $user['groupId']);
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
}
