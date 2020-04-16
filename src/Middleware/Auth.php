<?php

namespace Miaoxing\Plugin\Middleware;

use Miaoxing\Plugin\BaseController;
use Miaoxing\Services\Middleware\BaseMiddleware;

/**
 * @mixin \UserMixin
 * @mixin \UrlMixin
 * @mixin \EventMixin
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

        $ret = $this->event->until('checkAuth');
        if (!$ret && !$this->user->isLogin()) {
            $ret = $this->err([
                'message' => '您好,请登录',
                'next' => $this->url('users/login'),
            ]);
        }

        if (!$ret) {
            return $next();
        }

        return $this->redirectLogin($ret);
    }

    /**
     * 跳转到登录地址,或者返回包含登录信息的JSON
     *
     * @param string $url
     * @return array|\Wei\Response
     */
    protected function redirectLogin($ret)
    {
        if ($this->request->acceptJson()) {
            $ret['code'] = $ret['code'] !== -1 ? $ret['code'] : 401;
            $ret['next'] = $this->url->append($ret['next'], ['next' => $this->request->getReferer()]);
            return $ret;
        }

        return $this->response->redirect($this->url->append($ret['url'], ['next' => $this->request->getUrl()]));
    }
}
