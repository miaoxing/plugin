<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\UserModel;
use Miaoxing\Plugin\Test\BaseTestCase;

class UserModelTest extends BaseTestCase
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
}
