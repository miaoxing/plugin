<?php

namespace miaoxing\plugin;

/**
 * @property \Wei\Env $env
 * @property \Wei\View $view
 * @property \Wei\Event $event
 * @property \services\Logger $logger
 * @property \Wei\Request $request
 */
class BasePlugin extends \miaoxing\plugin\BaseService
{
    /**
     * 插件的简短名称
     *
     * @var string
     */
    protected $name = '';

    /**
     * 插件的版本号
     *
     * @var string
     * @link http://semver.org/lang/zh-CN/ 语义化版本2.0.0
     */
    protected $version = '1.0.0';

    /**
     * 插件的功能,使用说明描述
     *
     * @var string
     */
    protected $description = '';

    /**
     * 插件的唯一数字ID
     *
     * @var int
     */
    protected $id;

    /**
     * The web path for this plugin
     *
     * @var string
     */
    protected $basePath;

    /**
     * 安装当前插件
     *
     * @return array
     */
    public function install()
    {
        return ['code' => 1, 'message' => 'Install success'];
    }

    /**
     * 卸载当前插件
     *
     * @return array
     */
    public function uninstall()
    {
        return ['code' => 1, 'message' => 'Uninstall success'];
    }

    /**
     * 获取插件基本信息
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->name,
            'version' => $this->version,
            'description' => $this->description,
        ];
    }

    /**
     * 获取插件ID
     *
     * @return string
     */
    public function getId()
    {
        $class = get_class($this);
        $id = lcfirst(explode('\\', $class)[1]);
        if (substr($class, 0, 8) == 'Miaoxing') {
            $id = $this->dash($id);
        }

        return $id;
    }

    protected function dash($name)
    {
        return strtolower(preg_replace('/([A-Z])/', '-$1', $name));
    }

    /**
     * 加载插件的各项资源
     *
     * @param string $id
     */
    protected function initResources($id = null)
    {
        $id || $id = $this->getId();
        $dir = 'plugins/' . $id;
        $namespace = strtr($dir, '/', '\\');

        // 1. 加载项目配置
        $this->env->loadConfigDir($dir . '/configs');

        // 2. 加载项目服务类
        $serviceDir = $dir . '/services';
        if (is_dir($serviceDir)) {
            $this->wei->import($dir . '/services', $namespace . '\services');
        }

        // 3. 控制器继承
        $this->app->setControllerFormat($namespace . '\controllers\%controller%');

        // 4. 视图继承
        $this->view->setDirs([$dir . '/views'] + $this->view->getDirs());
    }

    /**
     * 加载插件的各项资源
     *
     * @param string $id
     */
    protected function initResourcesV2($id = null)
    {
        $id || $id = $this->getId();
        $plugin = $this->plugin->getById($id);
        $basePath = $plugin->getBasePath();

        $class = get_class($this);
        $namespace = substr($class, 0, strrpos($class, '\\'));

        // 1. 加载项目配置
        $this->env->loadConfigDir($basePath . '/configs');

        // 2. 加载项目服务类
        $serviceDir = $basePath . 'src/Service';
        if (is_dir($serviceDir)) {
            $this->wei->import($serviceDir, $namespace . '\Service');
        }

        // 3. 控制器继承
        $this->app->setControllerFormat($namespace . '\Controller\%controller%');

        // 4. 视图继承
        $this->view->setDirs([$basePath . '/resources/views'] + $this->view->getDirs());
    }

    /**
     * Output a rendered template by current event template
     *
     * @param array $data
     */
    protected function display($data = [])
    {
        $this->view->display($this->getEventTemplate(), $data);
    }

    /**
     * Get the view template file for current event and plugin
     *
     * @return string
     */
    protected function getEventTemplate()
    {
        $id = $this->getId();

        return $id . ':' . $id . '/' . $this->event->getCurName() . $this->view->getExtension();
    }

    /**
     * Returns the web path for this plugin
     *
     * @return string
     */
    public function getBasePath()
    {
        if (!$this->basePath) {
            $class = new \ReflectionClass($this);
            $this->basePath = substr(dirname($class->getFileName()), strlen(getcwd()) + 1);

            // TODO 改为默认
            if (substr($class->getName(), 0, 8) == 'Miaoxing') {
                $this->basePath = dirname($this->basePath);
            }
        }

        return $this->basePath;
    }

    /**
     * @return array
     */
    public function getControllerMap()
    {
        $basePath = $this->getBasePath() . '/src';

        return $this->plugin->generateClassMap([$basePath], '/Controller/{*,*/*}.php', 'Controller');
    }
}
