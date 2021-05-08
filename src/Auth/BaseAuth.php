<?php

namespace Miaoxing\Plugin\Auth;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Service\UserModel;

abstract class BaseAuth extends BaseService
{
    public const CODE_UNAUTHORIZED = 401;

    abstract public function login(UserModel $user);

    abstract public function logout();

    abstract public function checkLogin();

    abstract public function getData();

    public function isLogin()
    {
        return $this->checkLogin()->isSuc();
    }

    protected function loginRet($message = '您好,请登录')
    {
        return err($message, self::CODE_UNAUTHORIZED);
    }
}
