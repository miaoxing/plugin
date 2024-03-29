<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\UserModel;
use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * @internal
 */
final class UserModelTest extends BaseTestCase
{
    public function testDisplayName()
    {
        $user = UserModel::new();
        $this->assertNull($user->displayName);

        $user->name = 'name';
        $this->assertSame('name', $user->displayName);

        $user->username = 'username';
        $this->assertSame('username', $user->displayName);

        $user->nickName = 'nickName';
        $this->assertSame('nickName', $user->displayName);
    }

    public function testSetPassword()
    {
        $user = UserModel::new();
        $user->setPlainPassword('test');
        $this->assertNotEquals('test', $user->password);
    }

    public function testVerifyPassword()
    {
        $user = UserModel::new();
        $this->assertFalse($user->verifyPassword('test'));

        $user->setPlainPassword('test');
        $this->assertTrue($user->verifyPassword('test'));
    }
}
