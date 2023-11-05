<?php

namespace Miaoxing\Plugin\Auth;

use Miaoxing\Plugin\Service\Jwt;
use Miaoxing\Plugin\Service\UserModel;

/**
 * @mixin \JwtMixin
 * @mixin \ReqMixin
 */
class JwtAuth extends BaseAuth
{
    protected $loginRet;

    public function login(UserModel $user)
    {
        $data = ['id' => $user->id];
        $token = $this->jwt->generate($data);
        $this->loginRet = suc(['token' => $token, 'data' => $data]);
        return suc([
            'message' => '登录成功',
            'token' => $token,
        ]);
    }

    public function logout()
    {
        $this->loginRet = err('已退出登录');
    }

    public function checkLogin()
    {
        if (null === $this->loginRet) {
            $auth = $this->req->getServer('HTTP_AUTHORIZATION', '');
            // Remove "Bearer " prefix
            $auth = explode(' ', $auth)[1] ?? $auth;
            if (!$auth) {
                $ret = $this->loginRet();
            } else {
                $ret = $this->jwt->verify($auth);
                if ($ret->isErr()) {
                    Jwt::CODE_EXPIRED === $ret->getCode() && $ret->setMessage('您的登录已过期，请重新登录');
                    $ret->setCode(static::CODE_UNAUTHORIZED);
                }
            }

            $this->loginRet = $ret;
        }
        return $this->loginRet;
    }

    public function getData()
    {
        $ret = $this->checkLogin();
        if ($ret->isErr()) {
            return [];
        }

        return [
            'id' => $ret['data']['id'],
        ];
    }
}
