<?php

namespace Miaoxing\Plugin\Auth;

use Lcobucci\JWT\Token;
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
        $token = $this->jwt->generate(['id' => $user->id]);
        return suc([
            'message' => '登录成功',
            'token' => (string) $token,
        ]);
    }

    public function logout()
    {
        $this->loginRet = err('已退出登录');
    }

    public function checkLogin()
    {
        if (is_null($this->loginRet)) {
            $auth = $this->req->getServer('HTTP_AUTHORIZATION');
            if (!$auth) {
                $ret = $this->loginRet();
            } else {
                $ret = $this->jwt->verify($auth);
                if ($ret->isErr()) {
                    $ret['code'] === Jwt::CODE_EXPIRED && $ret['message'] = '您的登录已过期，请重新登录';
                    $ret['code'] = static::CODE_UNAUTHORIZED;
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

        /** @var Token $token */
        $token = $ret['token'];
        return [
            'id' => $token->getClaim('id'),
        ];
    }
}
