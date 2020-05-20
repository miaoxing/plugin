<?php

namespace Miaoxing\Plugin;

use Miaoxing\Services\Service\Page;

/**
 * @mixin \UserMixin
 * @mixin \AppMixin
 * @mixin \SessionMixin
 * @mixin \ViewMixin
 * @mixin \LoggerMixin
 * @mixin \PluginMixin
 * @mixin \RetMixin
 * @mixin \PageMixin
 * @property bool controllerAuth
 * @property array actionAuths
 */
abstract class BaseController extends \Miaoxing\Services\App\BaseController
{
    use HandleRetTrait;

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
     * @return null|string
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
