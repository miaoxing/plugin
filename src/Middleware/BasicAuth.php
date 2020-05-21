<?php

namespace Miaoxing\Plugin\Middleware;

use Miaoxing\Services\Middleware\BaseMiddleware;
use Wei\RetTrait;

class BasicAuth extends BaseMiddleware
{
    use RetTrait;

    protected $validUsers;

    public function __invoke($next)
    {
        if (!$this->validUsers) {
            return $this->responseNotAuthorized();
        }

        $username = $this->request->getServer('PHP_AUTH_USER');
        $password = $this->request->getServer('PHP_AUTH_PW');

        // 检查用户名是否有效
        if (!in_array($username, $this->validUsers, true)) {
            return $this->responseNotAuthorized();
        }

        // 查找用户
        $user = wei()->user()->find(['username' => $username]);
        if (!$user) {
            return $this->responseNotAuthorized('用户名不存在或密码错误');
        }

        // 校验密码
        $validated = $user->verifyPassword($password);
        if (!$validated) {
            return $this->responseNotAuthorized('用户不存在或密码错误');
        }

        return $next();
    }

    protected function responseNotAuthorized($message = 'Not authorized')
    {
        $this->response
            ->setHeader('WWW-Authenticate', 'Basic realm="API Realm"')
            ->setStatusCode(401);

        return $this->err($message);
    }
}
