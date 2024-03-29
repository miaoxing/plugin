<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Service\Ret;

/**
 * @mixin \EnvMixin
 * @mixin \ViewMixin
 * @mixin \EventMixin
 * @mixin \LoggerMixin
 * @mixin \ReqMixin
 * @mixin \StrMixin
 * @mixin \AppMixin
 * @mixin \PluginMixin
 */
abstract class BasePlugin extends BaseService
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
     * @see http://semver.org/lang/zh-CN/ 语义化版本2.0.0
     */
    protected $version = '1.0.0';

    /**
     * 插件的功能,使用说明描述
     *
     * @var string
     */
    protected $description = '';

    /**
     * 插件的唯一数字代码
     *
     * 用于错误码等
     *
     * @var int
     */
    protected $code;

    /**
     * The web path for this plugin
     *
     * @var string
     */
    protected $basePath;

    /**
     * 安装当前插件
     *
     * @return Ret
     */
    public function install()
    {
        return suc('Install success');
    }

    /**
     * 卸载当前插件
     *
     * @return Ret
     */
    public function uninstall()
    {
        return suc('Uninstall success');
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
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * 获取插件ID
     *
     * @return string
     */
    public function getId()
    {
        $class = static::class;
        $id = lcfirst(explode('\\', $class)[1]);
        $id = $this->str->dash($id);

        return $id;
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
            if ('Miaoxing' == substr($class->getName(), 0, 8)) {
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

        return wei()->classMap->generate([$basePath], '/Controller/{*,*/*}.php', 'Controller', false);
    }

    public function getDepIds(): array
    {
        $ids = [];
        $composer = json_decode(file_get_contents($this->getBasePath() . '/composer.json'), true);
        $require = $composer['require'] ?? [];

        foreach ($require as $name => $version) {
            $id = explode('/', $name)[1] ?? '';
            if (!$id) {
                continue;
            }

            $plugin = $this->plugin->getById($id);
            if ($plugin) {
                array_unshift($ids, $id);
                $ids = array_merge($plugin->getDepIds(), $ids);
            }
        }

        return array_unique($ids);
    }

    /**
     * 加载插件的各项资源
     *
     * @param string $id
     */
    protected function initResources($id = null)
    {
        $id || $id = $this->getId();
        $plugin = $this->plugin->getById($id);
        $basePath = $plugin->getBasePath();

        $class = static::class;
        $namespace = substr($class, 0, strrpos($class, '\\'));

        // 1. 加载项目配置
        $this->env->loadConfigDir($basePath . '/configs');

        // 2. 加载项目服务类
        $serviceDir = $basePath . '/src/Service';
        if (is_dir($serviceDir)) {
            $this->wei->import($serviceDir, $namespace . '\Service');
        }

        // 3. 控制器继承
        $this->app->setControllerFormat($namespace . '\Controller\%controller%');

        // 4. 视图继承
        $this->view->setDirs([$basePath . '/views'] + $this->view->getDirs());
    }

    /**
     * Output a rendered template by current event template
     *
     * @param array $data
     */
    protected function display($data = [])
    {
        // eg onScript
        $function = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'];
        $event = lcfirst(substr($function, 2));
        $id = $this->getId();

        // eg @plugin/plugin/script.php
        $name = '@' . $id . '/' . $id . '/' . $event . $this->view->getExtension();

        $this->view->display($name, $data);
    }
}
