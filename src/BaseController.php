<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Middleware\Auth;
use Miaoxing\Services\Service\Page;
use Wei\RetTrait;

/**
 * @property \Miaoxing\Plugin\Service\App $app
 * @property \Wei\Session $session
 * @property \Wei\View $view
 * @property \Wei\Logger $logger
 * @property \Wei\Event $event
 * @property \Miaoxing\Plugin\Service\Plugin $plugin
 * @property \Miaoxing\Plugin\Service\CurUser $curUser 用户
 * @property \Miaoxing\User\Service\CurUserV2 $curUserV2 用户
 * @property \Wei\Ret $ret 返回值服务
 * @property Page $page
 * @property bool controllerAuth
 * @property array actionAuths
 */
abstract class BaseController extends \Wei\BaseController
{
    use RetTrait;

    /**
     * 控制器名称
     *
     * @var string
     */
    protected $controllerName;

    /**
     * @var array
     */
    protected $actionPermissions = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->event->trigger('preControllerInit', [$this]);

        $this->middleware(Auth::class);

        // 触发控制器初始化事件
        $this->event->trigger('controllerInit', [$this]);
    }

    /**
     * 获取当前控制器的名称
     *
     * @return string
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
