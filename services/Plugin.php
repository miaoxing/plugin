<?php

namespace miaoxing\plugin\services;

use miaoxing\plugin\BaseService;

/**
 * 插件管理器
 *
 * 注意: 启用调试模式下,Ctrl+F5可以刷新缓存
 *
 * @property \Wei\BaseCache $cache 常规缓存,用于记录插件对应的事件
 * @property \Wei\BaseCache $configCache 记录生成的配置数组,使用phpFileCache速度最快,但需要在开发过程中生成,
 *                                       或者线上服务器可写,否则可改为memcached,redis等缓存
 * @property \Wei\Event $event
 * @property \Wei\Request $request
 * @property \miaoxing\plugin\services\App $app
 */
class Plugin extends BaseService
{
    /**
     * The default priority for plugin event
     */
    const DEFAULT_PRIORITY = 100;

    /**
     * @var string
     */
    protected $curNamespace;

    /**
     * 插件所在的目录,允许使用通配符
     *
     * @var array
     */
    protected $dirs = [
        '.',
        'plugins/*',
        'vendor/*/*',
        'vendor/*/*/src'
    ];

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
     * 重复类的对应表
     *
     * @var array
     */
    protected $duplicates = [];

    /**
     * {@inheritdoc}
     */
    protected $providers = [
        'configCache' => 'phpFileCache',
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $options)
    {
        parent::__construct($options);

        $config = $this->getConfig();

        // If the plugin service is not constructed, the service container can't set config for it
        if (!$this->wei->isInstanced('plugin')) {
            $this->wei->set('plugin', $this);
        }

        // Load configs to services
        $this->wei->setConfig($config + [
                'event' => [
                    'loadEvent' => [$this, 'loadEvent'],
                ],
                'view' => [
                    'parseResource' => [$this, 'parseViewResource'],
                ],
                'asset' => [
                    'locateFile' => [$this, 'locateFile'],
                ],
            ]);
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
                ],
                'app' => [
                    'controllerMap' => $this->getAppControllerMap(),
                ],
                'plugin' => [
                    'pluginClasses' => $this->getPluginClasses(),
                ],
            ];
        });
    }

    /**
     * Get services defined in plugins
     *
     * @return array
     */
    protected function getWeiAliases()
    {
        return array_merge(
            $this->generateClassMap($this->dirs, '/Service/*.php', 'Service'),
            $this->generateClassMap($this->dirs, '/services/*.php', 'services')
        );
    }

    /**
     * Get controllers defined in plugins
     *
     * @return array
     */
    protected function getAppControllerMap()
    {
        return array_merge(
            $this->generateClassMap($this->dirs, '/Controller/{*,*/*}.php', 'Controller'),
            // TODO 暂时支持三级控制器,待控制器升级后改为两级
            $this->generateClassMap($this->dirs, '/controllers/{*,*/*,*/*/*}.php', 'controllers')
        );
    }

    /**
     * Get all plugin classes
     *
     * @return array
     */
    protected function getPluginClasses()
    {
        if (!$this->pluginClasses) {
            $files = $this->globByDirs($this->dirs, '/Plugin.php');
            foreach ($files as $file) {
                $class = $this->guessClassName($file);
                $names = explode('\\', $class);
                $name = $names[count($names) - 2];
                $name = lcfirst($name);
                $this->pluginClasses[$name] = $class;
            }
            ksort($this->pluginClasses);
        }

        return $this->pluginClasses;
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
        if (isset($resource[0]) && $resource[0] == '@') {
            list($pluginId, $file) = explode('/', $resource, 2);
            $pluginId = substr($pluginId, 1);
        }

        // @deprecated
        if (strpos($resource, ':') !== false) {
            list($pluginId, $file) = explode(':', $resource, 2);
        }

        if ($pluginId) {
            $plugin = $this->getOneById($pluginId);

            // TODO 默认
            $path = $plugin->getBasePath();
            if (substr(get_class($plugin), 0, 8) == 'Miaoxing') {
                $path .= '/resources';
            }

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
            $components['path'] .= '/views';
        }

        return $components;
    }

    /**
     * 判断请求是否要求刷新缓存
     *
     * @return bool
     */
    protected function isRefresh()
    {
        return $this->wei->isDebug() && $this->request->getServer('HTTP_PRAGMA') == 'no-cache';
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
            $this->configCache->remove($key);
        }

        return $this->configCache->get($key, function () use ($fn) {
            return $fn();
        });
    }

    /**
     * 获取插件目录下所有的插件对象
     *
     * @return \miaoxing\plugin\BasePlugin[]
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
     * @return \miaoxing\plugin\BasePlugin
     * @throws \Exception 当插件类不存在时
     */
    public function getOneById($id)
    {
        $plugin = $this->getById($id);
        if (!$plugin) {
            throw new \Exception(sprintf('Plugin "%s" not found', $id), 404);
        }

        return $plugin;
    }

    /**
     * 根据插件ID获取插件对象
     *
     * @param string $id
     * @return \miaoxing\plugin\BasePlugin|false
     */
    public function getById($id)
    {
        if (!isset($this->pluginInstances[$id])) {
            $class = $this->getPluginClass($id);
            if (!class_exists($class)) {
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
     * @return array
     */
    public function install($id)
    {
        $plugin = $this->getById($id);
        if (!$plugin) {
            return ['code' => -1, 'message' => 'Plugin not found'];
        }

        if ($this->isInstalled($id)) {
            return ['code' => -2, 'message' => 'Plugin has been installed'];
        }

        $ret = $plugin->install();
        if ($ret['code'] !== 1) {
            return $ret;
        }

        $pluginIds = $this->getInstalledIds();
        $pluginIds[] = $id;
        $this->setInstalledIds($pluginIds);

        $this->getEvents(true);

        return $ret;
    }

    /**
     * Uninstall a plugin by ID
     *
     * @param string $id
     * @return array
     */
    public function uninstall($id)
    {
        $plugin = $this->getById($id);
        if (!$plugin) {
            return ['code' => -3, 'message' => 'Plugin not found'];
        }

        if (!$this->isInstalled($id)) {
            return ['code' => -4, 'message' => 'Plugin not installed'];
        }

        $ret = $plugin->uninstall();
        if ($ret['code'] !== 1) {
            return $ret;
        }

        $pluginIds = $this->getInstalledIds();
        $key = array_search($id, $pluginIds);
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
        return in_array($id, $this->builtIns);
    }

    /**
     * Check if a plugin is installed
     *
     * @param string $id
     * @return bool
     */
    public function isInstalled($id)
    {
        return $this->isBuildIn($id) || in_array($id, $this->getInstalledIds());
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
     * 获取所有已安装插件的事件
     *
     * @param bool $fresh 是否刷新缓存,获得最新配置
     * @return array
     */
    public function getEvents($fresh = false)
    {
        if (!$this->events || $fresh == true) {
            $cacheKey = 'plugin-events-' . $this->app->getNamespace();

            // 清除已有缓存
            if ($fresh || $this->isRefresh()) {
                $this->cache->remove($cacheKey);
            }

            $this->events = $this->cache->get($cacheKey, function () {
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

                return $events;
            });
        }

        return $this->events;
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
            if (substr($method, 0, 2) != 'on') {
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

    /**
     * Generate class map
     *
     * @param array $dirs
     * @param string $pattern
     * @param string $type
     * @return array
     */
    protected function generateClassMap(array $dirs, $pattern, $type)
    {
        $map = [];
        $files = $this->globByDirs($dirs, $pattern);

        foreach ($files as $file) {
            $class = $this->guessClassName($file);
            $name = $this->getShortName($class, $type);

            $this->addDuplicates($map, $name, $class);
            $map[$name] = $class;
        }

        ksort($map);

        return $this->filterDuplicates($map, $type);
    }

    /**
     * Guest class name by file name
     *
     * @param string $file
     * @return string
     */
    protected function guessClassName($file)
    {
        // 假设为根目录
        if ($file[0] === '.') {
            $file = $this->curNamespace . '\\' . ltrim($file, './');
        }

        // 忽略开头的vendor目录
        if (substr($file, 0, 7) == 'vendor/') {
            $file = substr($file, 7);
        }

        if (strpos($file, '/src/') !== false) {
            require_once 'vendor/' . $file;
            $class = end(get_declared_classes());
            return $class;
        }

        // 移除结尾的.php扩展名,并替换目录为命名空间分隔符
        return strtr(substr($file, 0, -4), ['/' => '\\']);
    }

    /**
     * Return short name of class by specified type
     *
     * @param string $class
     * @param string $type
     * @return string
     */
    protected function getShortName($class, $type)
    {
        // 获取类名中,类型之后的半段
        // 如miaoxing\user\controllers\admin\User返回admin\User
        $name = explode('\\' . $type . '\\', $class, 2)[1];

        // 将名称转换为小写
        $pos = strrpos($name, '\\');
        $pos = $pos === false ? 0 : $pos + 1;
        $name[$pos] = lcfirst($name[$pos]);
        $name = lcfirst($name);

        return $name;
    }

    /**
     * Find files matching a pattern in specified directories
     *
     * @param array $dirs
     * @param string $pattern
     * @return array
     */
    protected function globByDirs(array $dirs, $pattern)
    {
        $dirs = implode(',', $dirs);
        $pattern = '{' . $dirs . '}' . $pattern;

        return glob($pattern, GLOB_BRACE | GLOB_NOSORT);
    }

    /**
     * 记录重复的类名
     *
     * @param array $map 类名和短名称的映射表
     * @param string $name 类名对应的短名称
     * @param string $class 完整类名
     */
    protected function addDuplicates(array $map, $name, $class)
    {
        if (isset($map[$name])) {
            $this->duplicates[$name][$map[$name]] = true;
            $this->duplicates[$name][$class] = true;
        }
    }

    /**
     * 通过继承关系,过滤重复的子类
     * 如果还剩重复的类,抛出异常
     *
     * @param array $map
     * @param string $mapName
     * @return array
     */
    protected function filterDuplicates(array $map, $mapName)
    {
        foreach ($this->duplicates as $name => $classes) {
            foreach ($classes as $class => $flag) {
                // 如果是继承某个主类,则认为是该主类是想要的类
                // 只考虑一个主类的情况
                $parent = get_parent_class($class);
                if ($parent && isset($this->duplicates[$name][$parent])) {
                    $map[$name] = $parent;
                    unset($this->duplicates[$name]);
                    break;
                }
            }
        }

        foreach ($this->duplicates as $name => $classes) {
            throw new \RuntimeException(sprintf(
                'Duplicate class for %s "%s", the classes are %s',
                $mapName,
                $name,
                implode(', ', array_keys($classes))
            ));
        }

        return $map;
    }

    /**
     * Returns installed plugin IDs
     *
     * @return array
     */
    protected function getInstalledIds()
    {
        return $this->app->getRecord()->get('pluginIds');
    }

    /**
     * Stores installed plugin IDs
     *
     * @param array $pluginIds
     * @return $this
     */
    protected function setInstalledIds(array $pluginIds)
    {
        $app = $this->app->getRecord();
        $app['pluginIds'] = array_filter($pluginIds);
        $app->save();

        return $this;
    }
}
