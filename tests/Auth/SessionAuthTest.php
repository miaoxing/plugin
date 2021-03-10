<?php

namespace MiaoxingTest\Plugin\Auth;

use Miaoxing\Plugin\Auth\SessionAuth;
use Miaoxing\Plugin\Service\User;
use Miaoxing\Plugin\Service\UserModel;
use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * @mixin \SessionMixin
 */
class SessionAuthTest extends BaseTestCase
{
    /**
     * @var UserModel|null
     */
    protected $newUser;

    public function testGetDataWontCauseUserKeyToNull()
    {
        $auth = new SessionAuth();
        $data = $auth->getData();
        $this->assertSame([], $data);
        $this->assertArrayNotHasKey('user', $this->session);
    }

    public function testEmptyUserSession()
    {
        $auth = new SessionAuth();
        $this->session['user'] = [];
        $this->assertFalse($auth->isLogin());
    }

    public function testCurUserSave()
    {
        $this->initSession();
        $curUser = wei()->user;

        $curUser->save(['nickName' => 'nickName2']);

        $user = $this->getUser();
        $this->assertEquals($curUser['id'], $user['id']);
        $this->assertEquals('nickName2', $curUser['nickName']);

        $query = wei()->db->getLastQuery();
        $sql = 'UPDATE mx_users SET id = ?, nick_name = ?, updated_at = ?, updated_by = ? ';
        $sql .= 'WHERE id = ?';
        $this->assertEquals($sql, $query);
    }

    public function testCurUserToArray()
    {
        $this->initSession();
        $user = $this->getUser();
        $curUser = wei()->user;

        $data = $curUser->toArray();

        $this->assertEquals($user['id'], $data['id']);
        $this->assertEquals($user['nickName'], $data['nickName']);
    }

    public function testGetDataFromDb()
    {
        $user = $this->getUser();
        $this->initSession();

        $curUser = wei()->user;

        $this->assertEquals($user['id'], $curUser['id']);
        $this->assertEquals('name', $curUser['name']);
        $this->assertEquals('test@example.com', $curUser['email']);

        $query = wei()->db->getLastQuery();
        $this->assertEquals(implode(' ', [
            'SELECT * FROM `mx_users`',
            'WHERE `app_id` = ? AND `id` = ? LIMIT 1',
        ]), $query);
    }

    public function testLoadBeforeSet()
    {
        $this->initSession();

        wei()->user->email = 'abc';
        $this->assertEquals('name', wei()->user->name);
    }

    /**
     * 模拟curUser未加载数据前的情况
     */
    protected function initSession()
    {
        wei()->user = new User([
            'wei' => $this->wei,
            'authClass' => SessionAuth::class,
        ]);

        $user = $this->getUser();
        $this->session['user'] = $user->toArray(['id']);
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
