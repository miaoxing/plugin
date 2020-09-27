<?php

namespace Miaoxing\Plugin\Auth;

use Miaoxing\Plugin\Service\UserModel;

/**
 * @mixin \SessionMixin
 */
class SessionAuth extends BaseAuth
{
    public function login(UserModel $user)
    {
        $this->session['user'] = ['id' => $user->id];
        return suc('登录成功');
    }

    public function logout()
    {
        unset($this->session['user']);
    }

    public function getData()
    {
        return $this->session['user'] ?? [];
    }

    public function checkLogin()
    {
        if (isset($this->session['user']['id'])) {
            return suc();
        }
        return $this->loginRet();
    }
}
