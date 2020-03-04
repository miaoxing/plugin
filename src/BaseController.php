<?php

namespace Miaoxing\Plugin;

use Miaoxing\Services\Service\Page;

/**
 * @property \Miaoxing\Plugin\Service\App $app
 * @property \Wei\Session $session
 * @property \Wei\View $view
 * @property \Wei\Logger $logger
 * @property \Miaoxing\Plugin\Service\Plugin $plugin
 * @property \Miaoxing\Plugin\Service\CurUser $curUser 用户
 * @property \Miaoxing\User\Service\CurUserV2 $curUserV2 用户
 * @property \Wei\Ret $ret 返回值服务
 * @property Page $page
 * @property bool controllerAuth
 * @property array actionAuths
 */
abstract class BaseController extends \Miaoxing\Services\App\BaseController
{
    /**
     * @var array
     * @deprecated
     */
    protected $actionPermissions = [];

    /**
     * 获取当前控制器的名称
     *
     * @return string
     * @deprecated
     */
    public function getControllerName()
    {
        // 控制器名称如"图文管理(备注)",忽略括号中的备注内容
        return explode('(', $this->controllerName)[0] ?: null;
    }

    /**
     * 从权限配置中获取当前操作的名称
     *
     * @return string|null
     * @deprecated
     */
    public function getActionName()
    {
        $action = $this->app->getAction();
        if (isset($this->actionPermissions[$action])) {
            return $this->actionPermissions[$action];
        }

        foreach ($this->actionPermissions as $actions => $name) {
            $actions = explode(',', $actions);
            if (in_array($action, $actions)) {
                return $name;
            }
        }

        return null;
    }
}
