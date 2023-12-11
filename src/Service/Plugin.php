<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;

/**
 * 插件管理器
 *
 * 注意: 启用调试模式下,Ctrl+F5可以刷新缓存
 *
 * @mixin \CacheMixin 常规缓存,用于记录插件对应的事件
 * @property \Wei\BaseCache $configCache 记录生成的配置数组,使用phpFileCache速度最快,但需要在开发过程中生成,
 *                                       或者线上服务器可写,否则可改为memcached,redis等缓存
 * @mixin \EventMixin
 * @mixin \ReqMixin
 * @mixin \AppMixin
 * @mixin \ClassMapMixin
 * @mixin \PageRouterMixin
 */
class Plugin extends BaseService
{
    /**
     * The default priority for plugin event
     */
    protected const DEFAULT_PRIORITY = 100;

    /**
     * 插件所在的目录,允许使用通配符
     *
     * @var array
     */
    protected $basePaths = [
        'src',
        'plugins/*/src',
    ];

    /**
     * Whether enable plugin class autoload or not
     *
     * @var bool
     */
    protected $autoload = true;

    /**
     * The service names to ignore when generating the plugin config cache
     *
     * @var string[]
     * @deprecated use @ignored in class doc comment
     */
    protected $ignoredServices = [];

    /**
     * A List of build-in plugins
     *
     * @var array
     */
    protected $builtIns = ['plugin'];

    /**
     * An array that stores plugin classes,
     * the key is plugin ID and value is plugin class name
     *
     * @var array
     */
    protected $pluginClasses = [];

    /**
     * The instanced plugin objects
     *
     * @var array
     */
    protected $pluginInstances = [];

    /**
     * 插件的事件缓存数组
     *
     * @var array
     */
    protected $events = [];

    /**
     * 插件事件是否已绑定的标志位
     *
     * @var array
     */
    protected $loadedEvents = [];

    /**
     * {@inheritdoc}
     */
    protected $providers = [
        'configCache' => 'phpFileCache',
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options = [])
    {
        // Trigger setAutoload
        if (!isset($options['autoload'])) {
            $options['autoload'] = $this->autoload;
        }

        parent::__construct($options);

        // If the plugin service is not constructed, the service container can't set config for it
        if (!$this->wei->isInstanced('plugin')) {
            $this->wei->set('plugin', $this);
        }

        // Load configs to services
        $this->loadConfig();
    }

    /**
     * Whether enable autoload or not
     *
     * @param bool $autoload
     * @return $this
     */
    public function setAutoload($autoload)
    {
        $this->autoload = (bool) $autoload;
        call_user_func(
            $autoload ? 'spl_autoload_register' : 'spl_autoload_unregister',
            [$this, 'autoload']
        );
        return $this;
    }

    /**
     * Receive plugin relatives service configs
     *
     * @param bool $refresh
     * @return array
     */
    public function getConfig($refresh = false)
    {
        return $this->getCache('plugins-config', $refresh, function () {
            return [
                'wei' => [
                    'aliases' => $this->getWeiAliases(),
                    'preload' => $this->getWeiPreload(),
                ],
                'plugin' => [
                    'pluginClasses' => $this->getPluginClasses(true),
                ],
                'pageRouter' => [
                    'pages' => $this->pageRouter->generatePages(),
                ],
            ];
        });
    }

    /**
     * 将@开头的文件路径,转换为真实的路径
     *
     * @param string $file
     * @return string
     */
    public function locateFile($file)
    {
        $components = $this->parseResource($file);
        if ($components['path']) {
            $path = dirname($components['path']) . '/public/';

            return $path . $components['file'];
        } else {
            return $components['file'];
        }
    }

    /**
     * Parse a resource and return the components contains path and file
     *
     * @param string $resource
     * @return array|false Returns false when resource is not starts with @
     */
    public function parseResource($resource)
    {
        $pluginId = $file = null;
        if (isset($resource[0]) && '@' == $resource[0]) {
            list($pluginId, $file) = explode('/', $resource, 2);
            $pluginId = substr($pluginId, 1);
        }

        if ($pluginId) {
            $plugin = $this->getOneById($pluginId);
            $path = $plugin->getBasePath() . '/views';

            return ['path' => $path, 'file' => $file];
        } else {
            return ['path' => null, 'file' => $resource];
        }
    }

    /**
     * Parse a view resource
     *
     * @param string $resource
     * @return array
     */
    public function parseViewResource($resource)
    {
        $components = $this->parseResource($resource);
        if ($components['path']) {
            $components['path'] .= '/';
        }

        return $components;
    }

    /**
     * 获取插件目录下所有的插件对象
     *
     * @return \Miaoxing\Plugin\BasePlugin[]
     */
    public function getAll()
    {
        $data = [];
        foreach ($this->pluginClasses as $id => $class) {
            $plugin = $this->getById($id);
            if ($plugin) {
                $data[] = $plugin;
            }
        }

        return $data;
    }

    /**
     * 根据插件ID获取插件对象
     *
     * @param string $id
     * @return \Miaoxing\Plugin\BasePlugin
     * @throws \Exception 当插件类不存在时
     */
    public function getOneById($id)
    {
        $plugin = $this->getById($id);
        if (!$plugin) {
            throw new \Exception(sprintf('Plugin "%s" not found', $id));
        }

        return $plugin;
    }

    /**
     * 根据插件ID获取插件对象
     *
     * @param string $id
     * @return false|\Miaoxing\Plugin\BasePlugin
     */
    public function getById($id)
    {
        if (!isset($this->pluginInstances[$id])) {
            $class = $this->getPluginClass($id);
            if (!$class || !class_exists($class)) {
                $this->pluginInstances[$id] = false;
            } else {
                $this->pluginInstances[$id] = new $class(['wei' => $this->wei]);
            }
        }

        return $this->pluginInstances[$id];
    }

    /**
     * Install a plugin by ID
     *
     * @param string $id
     * @return Ret
     */
    public function install($id)
    {
        $plugin = $this->getById($id);
        if (!$plugin) {
            return err('插件不存在');
        }

        $installedIds = $this->getInstalledIds();
        $toInstallIds = array_merge($plugin->getDepIds(), [$id]);

        $rets = [];
        foreach ($toInstallIds as $pluginId) {
            if (in_array($pluginId, $installedIds, true)) {
                $rets[] = err(['插件 %s 已安装过', $pluginId]);
                continue;
            }

            $plugin = $this->getById($pluginId);
            $ret = $plugin->install();
            if ($ret->isSuc()) {
                $rets[] = suc(['插件 %s 安装成功', $pluginId]);
                continue;
            }

            $ret['rets'] = $rets;
            return $ret;
        }

        $this->setInstalledIds(array_merge($installedIds, $toInstallIds));

        $this->getEvents(true);

        return suc(['rets' => $rets]);
    }

    /**
     * Uninstall a plugin by ID
     *
     * @param string $id
     * @return Ret
     */
    public function uninstall($id)
    {
        $plugin = $this->getById($id);
        if (!$plugin) {
            return err('插件不存在');
        }

        if (!$this->isInstalled($id)) {
            return err('插件未安装');
        }

        if ($this->isBuildIn($id)) {
            return err('不能卸载内置插件');
        }

        $ret = $plugin->uninstall();
        if ($ret->isErr()) {
            return $ret;
        }

        $pluginIds = $this->getInstalledIds();
        $key = array_search($id, $pluginIds, true);
        if (false === $key) {
            return err('插件未安装');
        }
        unset($pluginIds[$key]);
        $this->setInstalledIds($pluginIds);

        $this->getEvents(true);

        return $ret;
    }

    /**
     * Check if a plugin is build in
     *
     * @param string $id
     * @return bool
     */
    public function isBuildIn($id)
    {
        return in_array($id, $this->builtIns, true);
    }

    /**
     * 获取所有已安装插件的事件
     *
     * @param bool $fresh 是否刷新缓存,获得最新配置
     * @return array
     */
    public function getEvents($fresh = false)
    {
        if (!$this->events || true == $fresh) {
            $cacheKey = 'plugin-events-' . $this->app->getId();

            // 清除已有缓存
            if ($fresh || $this->isRefresh()) {
                $this->cache->delete($cacheKey);
            }

            $this->events = $this->cache->remember($cacheKey, function () {
                $events = [];
                foreach ($this->getAll() as $plugin) {
                    $id = $plugin->getId();
                    if (!$this->isInstalled($id)) {
                        continue;
                    }
                    foreach ($this->getEventsById($id) as $event) {
                        $events[$event['name']][$event['priority']][] = $id;
                    }
                }
                ksort($events);
                return $events;
            });
        }

        return $this->events;
    }

    /**
     * Load plugin event by name
     *
     * @param string $name
     */
    public function loadEvent($name)
    {
        // 1. Load event data only once
        if (isset($this->loadedEvents[$name])) {
            return;
        }
        $this->loadedEvents[$name] = true;

        // 2. Get event handlers
        $events = $this->getEvents();
        if (!isset($events[$name])) {
            return;
        }

        // 3. Attach handlers to event
        $baseMethod = 'on' . ucfirst($name);
        foreach ($events[$name] as $priority => $pluginIds) {
            if ($priority && $priority != static::DEFAULT_PRIORITY) {
                $method = $baseMethod . $priority;
            } else {
                $method = $baseMethod;
            }

            foreach ($pluginIds as $pluginId) {
                $plugin = $this->getById($pluginId);
                if (method_exists($plugin, $method)) {
                    $this->event->on($name, [$plugin, $method], $priority);
                }
            }
        }
    }

    public function getPluginIdByClass($class)
    {
        // 类名如:Miaoxing\App\Controller\Apps
        $id = explode('\\', $class, 3)[1];
        $id = $this->dash($id);

        return $id;
    }

    /**
     * @return array
     */
    public function getBasePaths()
    {
        return $this->basePaths;
    }

    /**
     * Load service configs
     *
     * @param bool $refresh
     * @return $this
     * @svc
     */
    protected function loadConfig($refresh = false)
    {
        // Load configs to services
        $config = $this->getConfig($refresh);
        $this->wei->setConfig($config + [
                'event' => [
                    'loadEvent' => [$this, 'loadEvent'],
                ],
                'view' => [
                    'parseResource' => [$this, 'parseViewResource'],
                ],
            ]);
        return $this;
    }

    /**
     * Get services defined in plugins
     *
     * @return array
     */
    protected function getWeiAliases()
    {
        return array_diff_key(
            $this->classMap->generate($this->basePaths, '/Service/*.php', 'Service'),
            array_flip($this->ignoredServices)
        );
    }

    /**
     * Get preload defined in composer.json
     *
     * @return array
     */
    protected function getWeiPreload()
    {
        $preload = [];
        $files = glob('plugins/*/composer.json');
        foreach ($files as $file) {
            $config = json_decode(file_get_contents($file), true);
            if (isset($config['extra']['wei-preload'])) {
                $preload = array_merge($preload, $config['extra']['wei-preload']);
            }
        }
        return $preload;
    }

    /**
     * Get all plugin classes
     *
     * @param bool $refresh
     * @return array
     * @throws \Exception
     */
    protected function getPluginClasses($refresh = false)
    {
        if ($refresh || !$this->pluginClasses) {
            $this->pluginClasses = [];
            $classes = $this->classMap->generate($this->basePaths, '/*Plugin.php', '', false, true);
            foreach ($classes as $class) {
                $parts = explode('\\', $class);
                $name = end($parts);
                // Remove "Plugin" suffix
                $name = substr($name, 0, -6);
                $name = $this->dash($name);
                $this->pluginClasses[$name] = $class;
            }
        }

        return $this->pluginClasses;
    }

    /**
     * 判断请求是否要求刷新缓存
     *
     * @return bool
     */
    protected function isRefresh()
    {
        return $this->wei->isDebug()
            && 'no-cache' == $this->req->getServer('HTTP_PRAGMA')
            && false === strpos($this->req->getServer('HTTP_USER_AGENT'), 'wechatdevtools');
    }

    /**
     * 执行指定的回调,并存储到缓存中
     *
     * @param string $key
     * @param bool $refresh
     * @param callable $fn
     * @return mixed
     */
    protected function getCache($key, $refresh, callable $fn)
    {
        if ($refresh || $this->isRefresh()) {
            $this->configCache->delete($key);
        }

        return $this->configCache->remember($key, static function () use ($fn) {
            return $fn();
        });
    }

    /**
     * Check if a plugin exists
     *
     * @param string $id
     * @return bool
     * @svc
     */
    protected function has($id)
    {
        return class_exists($this->getPluginClass($id));
    }

    /**
     * Check if a plugin is installed
     *
     * @param string $id
     * @return bool
     * @svc
     */
    protected function isInstalled($id)
    {
        return $this->isBuildIn($id) || in_array($id, $this->getInstalledIds(), true);
    }

    /**
     * Returns the plugin class by plugin ID
     *
     * @param string $id
     * @return string
     */
    protected function getPluginClass($id)
    {
        return isset($this->pluginClasses[$id]) ? $this->pluginClasses[$id] : null;
    }

    /**
     * Returns the event definitions by plugin ID
     *
     * @param string $id
     * @return array
     */
    protected function getEventsById($id)
    {
        $events = [];
        $methods = get_class_methods($this->getPluginClass($id));
        foreach ($methods as $method) {
            // The event naming is onName[Priority],eg onProductShowItem50
            if ('on' != substr($method, 0, 2)) {
                continue;
            }
            $event = lcfirst(substr($method, 2));
            if (is_numeric(substr($event, -1))) {
                preg_match('/(.+?)(\d+)$/', $event, $matches);
                $events[] = ['name' => $matches[1], 'priority' => (int) $matches[2]];
            } else {
                $events[] = ['name' => $event, 'priority' => static::DEFAULT_PRIORITY];
            }
        }

        return $events;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function dash($name)
    {
        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '-$1', $name));
    }

    /**
     * @param string $class
     * @return bool
     */
    protected function autoload($class)
    {
        if (0 !== strpos($class, 'Miaoxing\\')) {
            return false;
        }

        // Ignore prefix namespace
        [$ignore, $name, $path] = explode('\\', $class, 3);

        $ds = \DIRECTORY_SEPARATOR;
        $file = implode($ds, ['plugins', $this->dash($name), 'src', strtr($path, ['\\' => $ds])]) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }

        return false;
    }

    /**
     * Returns installed plugin IDs
     *
     * @return array
     */
    protected function getInstalledIds()
    {
        return (array) $this->app->getModel()->get('pluginIds');
    }

    /**
     * Stores installed plugin IDs
     *
     * @param array $pluginIds
     * @return $this
     */
    protected function setInstalledIds(array $pluginIds)
    {
        $app = $this->app->getModel();
        $app['pluginIds'] = array_filter(array_unique($pluginIds));
        $app->save();

        return $this;
    }
}
