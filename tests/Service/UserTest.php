<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\User;
use Miaoxing\Plugin\Service\UserModel;
use Miaoxing\Plugin\Test\BaseTestCase;

class UserTest extends BaseTestCase
{
    public function testCur()
    {
        $user = User::cur();
        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(UserModel::class, $user);

        $user2 = User::cur();
        $this->assertSame($user, $user2);
    }

    public function testId()
    {
        User::loginById(1);
        $this->assertSame(1, User::id());

        User::logout();
        $this->assertNull(User::id());
    }
}
