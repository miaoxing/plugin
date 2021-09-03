<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\User;
use Miaoxing\Plugin\Service\UserModel;
use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * @internal
 */
final class UserTest extends BaseTestCase
{
    /**
     * @var UserModel|null
     */
    protected $newUser;

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
        User::loginById('1');
        $this->assertSame('1', User::id());

        User::logout();
        $this->assertNull(User::id());
    }

    public function testGetIdWithoutLogin()
    {
        User::logout();
        $this->assertNull(User::id());
        $this->assertSame('', User::cur()->name);
    }

    public function testUserSaveRefreshCurUserData()
    {
        $curUser = User::cur();
        $curUser->logout();

        $user = $this->getUser();
        $curUser->loginByModel($user);
        $this->assertEquals('nickName', $curUser->nickName);

        $user->save(['nickName' => 'nickName2']);
        $this->assertEquals('nickName2', $curUser->nickName);
    }

    public function testLoginByModelAndSave()
    {
        $user = $this->getUser();
        $curUser = User::cur();

        $ret = $curUser->loginByModel($user);
        $this->assertRetSuc($ret);

        $curUser->save(['nickName' => 'nickName2']);

        $this->assertEquals('nickName2', $curUser['nickName']);

        $query = wei()->db->getLastQuery();
        $sql = 'UPDATE mx_users SET nick_name = ?, updated_by = ? WHERE id = ?';
        $this->assertEquals($sql, $query);
    }

    public function testLogout()
    {
        $user = $this->getUser();
        $curUser = User::cur();
        $curUser->loginByModel($user);

        $this->assertEquals($user->id, $curUser->id);

        $curUser->logout();

        $this->assertFalse($curUser->isLogin());
        $this->assertNull($curUser->id);
    }

    public function testGetId()
    {
        $user = $this->getUser();
        User::loginByModel($user);

        $this->assertIsString(User::cur()->id);
    }

    protected function getUser()
    {
        $this->newUser || $this->newUser = UserModel::save([
            'nickName' => 'nickName',
            'email' => 'test@example.com',
            'name' => 'name',
        ]);

        return $this->newUser;
    }
}
