<?php

namespace Miaoxing\Plugin\Middleware;

class BasicAuth extends Base
{
    protected $validUsers;

    public function __invoke($next)
    {
        if (!$this->validUsers) {
            return $this->responseNotAuthorized();
        }

        $username = $this->request->getServer('PHP_AUTH_USER');
        $password = $this->request->getServer('PHP_AUTH_PW');

        // 检查用户名是否有效
        if (!in_array($username, $this->validUsers)) {
            return $this->responseNotAuthorized();
        }

        // 查找用户
        $user = wei()->user()->find(['username' => $username]);
        if (!$user) {
            return $this->responseNotAuthorized();
        }

        // 校验密码
        $validated = $user->verifyPassword($password);
        if (!$validated) {
            return $this->responseNotAuthorized();
        }

        return $next();
    }

    protected function responseNotAuthorized()
    {
        return $this->response
            ->setHeader('WWW-Authenticate', 'Basic realm="API Realm"')
            ->setStatusCode(401)
            ->setContent('Not authorized');
    }
}
