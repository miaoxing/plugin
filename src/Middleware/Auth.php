<?php

namespace Miaoxing\Plugin\Middleware;

use Miaoxing\Plugin\BaseController;
use Miaoxing\Services\Middleware\BaseMiddleware;

/**
 * @mixin \UserMixin
 * @mixin \UrlMixin
 */
class Auth extends BaseMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($next, BaseController $controller = null)
    {
        // 检查控制器是否需要登录
        if ($controller->getOption('controllerAuth') === false) {
            return $next();
        }

        // 检查操作是否需要登录
        $action = $this->app->getAction();
        $auths = $controller->getOption('actionAuths');
        if (isset($auths[$action]) && $auths[$action] === false) {
            return $next();
        }

        if ($this->user->isLogin()) {
            return $next();
        }

        // 跳转到相应的登录页面
        $url = wei()->event->until('loginUrl') ?: $this->url('users/login');
        return $this->redirectLogin($url);
    }

    /**
     * 跳转到登录地址,或者返回包含登录信息的JSON
     *
     * @param string $url
     * @return array|\Wei\Response
     */
    protected function redirectLogin($url)
    {
        if ($this->request->acceptJson()) {
            $url = $this->url->append($url, ['next' => $this->request->getReferer()]);

            return $this->err([
                'code' => 401,
                'message' => '您好,请登录',
                'redirect' => $url,
            ]);
        } else {
            $url = $this->url->append($url, ['next' => $this->request->getUrl()]);

            return $this->response->redirect($url);
        }
    }
}
