<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Service\Asset;
use Miaoxing\Plugin\Service\Page;
use Wei\RetTrait;
use Miaoxing\Plugin\Middleware\Auth;

/**
 * @property \Miaoxing\Plugin\Service\App $app
 * @property \Wei\Request $request
 * @method   string request($name, $default = '')
 * @property \Wei\Response $response
 * @property \Wei\Session $session
 * @property \Wei\View $view
 * @property \Wei\Logger $logger
 * @property \Wei\Event $event
 * @property \Miaoxing\Plugin\Service\Plugin $plugin
 * @property \Miaoxing\Plugin\Service\User $user 用户
 * @property \Miaoxing\Plugin\Service\CurUser $curUser 用户
 * @property \Miaoxing\User\Service\CurUserV2 $curUserV2 用户
 * @property \Miaoxing\Order\Service\Url $url URL生成服务
 * @method   string url($url = '', $argsOrParams = array(), $params = array())
 * @property \Wei\Ret $ret 返回值服务
 * @property \Miaoxing\Plugin\Service\Setting $setting
 * @method   string setting($name, $default = null) 读取配置
 * @property \Miaoxing\Queue\Service\BaseQueue $queue 队列服务
 * @property Asset wpAsset
 * @property Page $page
 */
abstract class BaseController extends \Wei\BaseController
{
    use RetTrait;

    /**
     * 后台当前头部导航的编号
     *
     * @var string
     */
    protected $adminNavId;

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
            'controllerInstance' => $this,
            'js' => &$this->js,
        ]);

        // 设置页面名称
        if (!isset($this->view['controllerName'])) {
            $this->view['controllerName'] = $this->controllerName;
        }
        $this->js += [
            'page' => [
                'controllerTitle' => $this->getControllerName(),
                'actionTitle' => $this->getActionName(),
            ],
            'webpackPublicPath' => $this->wpAsset->getRevPrefix(),
            'pluginIds' => $this->app->getRecord()['pluginIds'],
        ];

        // 为后台设置默认布局
        if ($this->app->isAdmin() && $this->plugin->has('admin')) {
            $this->view->setDefaultLayout('admin:admin/layout.php');
        }
    }

    /**
     * 返回指定格式的JSON响应
     *
     * @param string $message
     * @param int $code
     * @param array $append
     * @return \Wei\Response
     * @deprecated 成功使用suc,失败使用err
     */
    protected function json($message = '操作成功', $code = 1, array $append = [])
    {
        return $this->response->json(['message' => $message, 'code' => $code] + $append);
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
