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
        User::loginByModel($this->getUser());

        $user = User::cur();
        $this->assertNotInstanceOf(User::class, $user);
        $this->assertInstanceOf(UserModel::class, $user);

        $user2 = User::cur();
        $this->assertSame($user, $user2);
    }

    public function testId()
    {
        $user = $this->getUser();
        User::loginById($user->id);
        $this->assertSame($user->id, User::id());

        User::logout();
        $this->assertNull(User::id());
    }

    public function testGetIdWithoutLogin()
    {
        User::logout();
        $this->assertNull(User::id());
        $this->assertNull(User::get('name'));
    }

    public function testUserSaveRefreshCurUserData()
    {
        $curUser = User::instance();
        $curUser->logout();

        $user = $this->getUser();
        $curUser->loginByModel($user);
        $this->assertEquals('nickName', $curUser->get('nickName'));

        $user->save(['nickName' => 'nickName2']);
        $this->assertEquals('nickName2', $curUser->get('nickName'));
    }

    public function testLoginByModelAndSave()
    {
        $user = $this->getUser();

        $ret = User::loginByModel($user);
        $this->assertRetSuc($ret);

        User::save(['nickName' => 'nickName2']);

        $this->assertEquals('nickName2', User::get('nickName'));

        $query = wei()->db->getLastQuery();
        $sql = 'UPDATE mx_users SET nick_name = ?, updated_by = ? WHERE id = ?';
        $this->assertEquals($sql, $query);
    }

    public function testLogout()
    {
        $user = $this->getUser();
        User::loginByModel($user);

        $this->assertEquals($user->id, User::id());

        User::logout();

        $this->assertFalse(User::isLogin());
        $this->assertNull(User::id());
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
