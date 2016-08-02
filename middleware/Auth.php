<?php

namespace miaoxing\plugin\middleware;

/**
 * @property \Wei\Ret $ret
 * @property \Wei\Url $url
 * @method string url($url = '', $argsOrParams = array(), $params = array())
 * @property \services\Logger $logger
 */
class Auth extends Base
{
    protected $guestPages = [];

    protected $adminGuestPages = [];

    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        // 1. 游客页面一律不用登录
        $page = $this->app->getControllerAction();
        if ($this->isBelongPages($page, $this->guestPages)) {
            return $next();
        }

        // 2. 触发用户初始化事件,允许插件初始化用户
        $res = wei()->event->until('userInit');
        /** @var \Wei\Response $res */
        if ($res) {
            $this->logger->info('Got response after user init, response header is', $res->getHeaderString());
            return $res;
        }

        // 3. 如果是后台页面,验证登录态和用户权限
        $isLogin = wei()->curUser->isLogin();

        if ($this->isAdminPage()) {
            // 如果未登录,跳转到登录页面
            if (!$isLogin) {
                return $this->redirectLogin($this->getAdminLoginUrl());
            }

            // 后台登录无权限,跳转到登录页面,并展示提示
            if (!wei()->curUser->isAdmin()) {
                return $this->redirectLogin($this->getAdminLoginUrl('很抱歉,您没有权限查看当前页面'));
            }

            if (!$this->isBelongPages($page, $this->adminGuestPages)) {
                // 触发后台权限检查事件
                $res = wei()->event->until('adminAuth', [$page, wei()->curUser]);
                if ($res) {
                    return $res;
                }
            }
        }

        // 3. 跳转到登录页面
        if (!$isLogin) {
            return $this->redirectLogin($this->url('users/login'));
        }

        return $next();
    }

    /**
     * 通过控制器中是否包含admin,判断是否为后台控制器
     *
     * @return bool
     */
    protected function isAdminPage()
    {
        return strpos($this->app->getController(), 'admin') !== false;
    }

    /**
     * 获取登录地址
     *
     * @param string $message 附带的提示信息
     * @return string
     */
    public function getAdminLoginUrl($message = '')
    {
        return $this->url('admin/login', ['message' => $message]);
    }

    /**
     * 跳转到登录地址,或者返回包含登录信息的JSON
     *
     * @param string $url
     * @return \Wei\Response
     */
    protected function redirectLogin($url)
    {
        if ($this->request->acceptJson()) {
            $url = $this->url->append($url, ['next' => $this->request->getReferer()]);
            return $this->response->json([
                'code' => -401,
                'message' => '您好,请登录',
                'redirect' => $url,
            ]);
        } else {
            $url = $this->url->append($url, ['next' => $this->request->getUrl()]);
            return $this->response->redirect($url);
        }
    }

    /**
     * 检查指定页面是否在某组页面中
     *
     * @param string $page
     * @param array $allowPages
     * @return bool
     */
    protected function isBelongPages($page, array $allowPages)
    {
        foreach ($allowPages as $guestPage) {
            if (strpos($page, $guestPage) === 0) {
                return true;
            }
        }
        return false;
    }
}
