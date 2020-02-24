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
 */
abstract class BaseController extends \Wei\BaseController
{
    use RetTrait;

    /**
     * 当前控制器名称
     *
     * @var string
     */
    protected $controllerName;

    /**
     * @var array
     */
    protected $actionPermissions = [];

    /**
     * 未登录用户可以访问的页面前缀
     *
     * @var array
     */
    protected $guestPages = [
        'cli',
        // 非多级控制器在子类中配置
    ];

    /**
     * 后台不受权限管理控制的页面
     *
     * @var array
     */
    protected $adminGuestPages = [];

    /**
     * @var array
     */
    protected $js = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->event->trigger('preControllerInit', [$this]);

        $this->initViewVars();

        $this->middleware(Auth::class, [
            'guestPages' => $this->guestPages,
            'adminGuestPages' => $this->adminGuestPages,
        ]);

        // 触发控制器初始化事件
        $this->event->trigger('controllerInit', [$this]);
    }

    /**
     * 初始化常用的视图变量
     */
    protected function initViewVars()
    {
        $this->view->assign([
            'js' => &$this->js,
        ]);

        $this->js += $this->app->getConfig() + [
                'theme' => $this->page->theme,
                'pluginIds' => $this->app->getRecord()['pluginIds'],
                'pageMap' => $this->app->pageMap,
            ];
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
