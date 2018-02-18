<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Service\Asset;
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
 * @property \Miaoxing\Order\Service\Url $url URL生成服务
 * @method   string url($url = '', $argsOrParams = array(), $params = array())
 * @property \Wei\Ret $ret 返回值服务
 * @property \Miaoxing\Plugin\Service\Setting $setting
 * @method   string setting($name, $default = null) 读取配置
 * @property \Miaoxing\Queue\Service\BaseQueue $queue 队列服务
 * @property Asset wpAsset
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
        'mall', 'cli',
        // 非多级控制器在子类中配置
    ];

    /**
     * 后台不受权限管理控制的页面
     *
     * @var array
     */
    protected $adminGuestPages = [];

    /**
     * 页面配置
     *
     * @var \ArrayObject
     */
    protected $pageConfig;

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
        // TODO 待理清,抽象为页面服务
        $this->pageConfig = new \ArrayObject([
            'displayHeader' => true,
            'displayFooter' => true,
        ]);

        $controller = $this->app->getController();
        $this->view->assign([
            // Services
            'e' => $this->e,
            'block' => $this->block,
            'app' => $this->app,
            'url' => $this->url,
            'asset' => $this->asset,
            'event' => $this->event,
            'pageConfig' => $this->pageConfig,
            'controllerInstance' => $this,
            'js' => &$this->js,
        ]);

        // TODO 移除依赖
        // 非自带服务先改为按需加载,便于测试
        $customServices = ['curUser', 'setting'];
        foreach ($customServices as $service) {
            if ($this->wei->has($service)) {
                $this->view->assign($service, $this->$service);
            }
        }

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
        ];

        // 为后台设置默认布局
        if (strpos($controller, 'admin') !== false && $this->plugin->has('admin')) {
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
     * @param string $key
     * @param $value
     */
    public function setPageConfig($key, $value)
    {
        $this->pageConfig[$key] = $value;
    }

    /**
     * 获取当前控制器的名称
     *
     * @return string
     */
    public function getControllerName()
    {
        // 控制器名称如"图文管理(备注)",忽略括号中的备注内容
        return explode('(', $this->controllerName)[0];
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
