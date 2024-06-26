<?php

namespace Miaoxing\Plugin\Service;

use Wei\BaseController;
use Wei\BaseController as WeiBaseController;
use Wei\Res;
use Wei\Ret\RetException;

/**
 * 应用
 *
 * @mixin \EventMixin
 * @mixin \StrMixin
 * @mixin \AppModelMixin
 * @mixin \CacheMixin
 * @mixin \PageRouterMixin
 * @mixin \ConfigMixin
 */
class App extends \Wei\App
{
    protected const NOT_FOUND = 404;

    protected const METHOD_NOT_ALLOWED = 405;

    /**
     * 插件控制器不使用该格式,留空可减少类查找
     *
     * {@inheritdoc}
     */
    protected $controllerFormat = '';

    /**
     * {@inheritdoc}
     */
    protected $actionMethodFormat = '%action%';

    /**
     * 当前运行的插件名称
     *
     * @var false|string
     */
    protected $plugin = false;

    /**
     * 默认域名
     *
     * 如果请求的默认域名,就不到数据库查找域名
     *
     * @var array
     */
    protected $domains = [];

    /**
     * @var string
     */
    protected $defaultViewFile = '@plugin/_default.php';

    /**
     * @var string|null
     */
    protected $fallbackPathInfo;

    /**
     * Whether the application is in demo mode
     *
     * @var bool
     */
    protected $isDemo = false;

    /**
     * The id of the current application
     *
     * @var string
     */
    protected $id;

    /**
     * 应用模型缓存
     *
     * @var AppModel[]
     */
    protected $models = [];

    /**
     * @var array
     * @internal
     */
    protected $pathMap = [
        '/admin-api/' => '/api/admin/',
        '/api/admin/' => '/admin-api/',
        '/m-api/' => '/api/',
        '/api/' => '/m-api/',
    ];

    /**
     * @var WeiBaseController|null
     */
    private $curControllerInstance;

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $options = [])
    {
        // Load global config
        $this->config->preloadGlobal();

        $this->event->trigger('appInit');

        return $this->invokeApp($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTemplate($controller = null, $action = null)
    {
        $file = $controller ?: $this->controller;
        $file = dirname($file) . '/_' . basename($file);

        $plugin = $this->getPlugin();

        return $plugin ? '@' . $plugin . '/' . $file : $file;
    }

    /**
     * 获取当前插件下的视图文件,即可省略当前插件名称不写
     *
     * @param string $name
     * @return string
     */
    public function getPluginFile($name)
    {
        return $this->view->getFile('@' . $this->getPlugin() . '/' . $name);
    }

    /**
     * 获取当前运行的插件名称
     *
     * @return string
     */
    public function getPlugin()
    {
        if (!$this->plugin && $this->controller) {
            // 认为第二部分是插件名称
            [, $plugin] = explode('/', $this->controller, 3);
            $this->plugin = $plugin;
        }
        return $this->plugin;
    }

    /**
     * Return the current application model object
     *
     * @return AppModel
     * @throws \Exception When the application not found
     */
    public function getModel(): AppModel
    {
        $id = $this->getId();
        if (!isset($this->models[$id])) {
            $model = AppModel::new();
            $this->models[$id] = $model
                ->setCacheKey($model->getModelCacheKey($id))
                ->setCacheTime(86400)
                ->findOrFail($id);
        }
        return $this->models[$id];
    }

    /**
     * Set the current application model object
     *
     * @param AppModel|null $model
     * @return $this
     */
    public function setModel(?AppModel $model): self
    {
        $this->models[$this->getId()] = $model;
        return $this;
    }

    /**
     * Set the id of the current application
     *
     * @param string|null $id
     * @return $this
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return the id of the current application
     *
     * @return string
     */
    public function getId(): string
    {
        if (!$this->id) {
            $this->id = $this->detectId();
        }
        return $this->id;
    }

    /**
     * 重写handleResponse,支持Ret结构
     *
     * @param mixed $response
     * @return Res
     * @throws \Exception
     */
    public function handleResponse($response)
    {
        if ($response instanceof Ret) {
            return $response->toRes($this->req, $this->res);
        } elseif ($response instanceof \JsonSerializable) {
            return $this->res->json($response);
        } elseif (is_array($response)) {
            $template = $this->getDefaultTemplate();
            $file = $this->view->resolveFile($template) ? $template : $this->defaultViewFile;
            $content = $this->view->render($file, $response);
            return $this->res->setContent($content);
        } else {
            return parent::handleResponse($response);
        }
    }

    /**
     * 判断是否请求到后台页面
     *
     * @return bool
     */
    public function isAdmin()
    {
        // NOTE: 控制器不存在时，回退的控制器不带有 admin
        return false !== strpos($this->req->getRouterPathInfo(), '/admin/');
    }

    /**
     * 设置默认视图文件
     *
     * @param string $defaultViewFile
     * @return $this
     */
    public function setDefaultViewFile($defaultViewFile)
    {
        $this->defaultViewFile = $defaultViewFile;
        return $this;
    }

    /**
     * @return WeiBaseController
     * @experimental
     */
    public function getCurControllerInstance(): WeiBaseController
    {
        if (!$this->curControllerInstance) {
            $this->curControllerInstance = require $this->controller;
        }
        return $this->curControllerInstance;
    }

    /**
     * Returns whether the application is in demo mode
     *
     * @return bool
     * @svc
     */
    protected function isDemo(): bool
    {
        return $this->isDemo;
    }

    protected function invokeApp(array $options = [])
    {
        $options && $this->setOption($options);

        $pathInfo = $this->req->getRouterPathInfo();

        $result = $this->matchPathInfo($pathInfo);
        if (!$result) {
            throw new \Exception('Not Found', static::NOT_FOUND);
        }

        $action = strtolower($this->req->getMethod());
        return $this->dispatch($result['file'], $action, $result['params']);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($controller, $action = null, array $params = [], $throwException = true /* ignored */)
    {
        $this->setController($controller);
        $this->setAction($action);
        $this->req->set($params);

        $page = $this->getCurControllerInstance();

        // TODO allow execute middleware before action
        if ('options' !== $action && !method_exists($page, $action)) {
            $this->res->setStatusCode(static::METHOD_NOT_ALLOWED);
            throw new \Exception('Method Not Allowed', static::METHOD_NOT_ALLOWED);
        }

        return $this->execute($page, $action);
    }

    /**
     * @param BaseController $instance
     * @param string $action
     * @return Res
     * @throws \Exception
     */
    protected function execute($instance, $action)
    {
        $wei = $this->wei;

        $instance->init();
        $middleware = $this->getMiddleware($instance, $action);

        $callback = function () use ($instance, $action) {
            $instance->before($this->req, $this->res);

            $method = $this->getActionMethod($action);
            // TODO 和 forward 异常合并一起处理
            try {
                $response = $instance->{$method}($this->req, $this->res);
            } catch (RetException $e) {
                return $e->getRet();
            }

            $instance->after($this->req, $response);

            return $response;
        };

        $next = static function () use (&$middleware, &$next, $callback, $wei, $instance) {
            $config = array_splice($middleware, 0, 1);
            if ($config) {
                $class = key($config);
                $service = new $class(['wei' => $wei] + $config[$class]);
                $result = $service($next, $instance);
            } else {
                $result = $callback();
            }

            return $result;
        };

        return $this->handleResponse($next())->send();
    }

    /**
     * Detect the id of application
     *
     * @return string
     */
    protected function detectId(): string
    {
        // 1. Domain
        if ($id = $this->getIdByDomain()) {
            return $id;
        }

        // 2. Request parameter
        if ($id = $this->req->get('appId')) {
            return $id;
        }

        // 3. First id from database
        return $this->cache->remember('app:firstId', 86400, static function () {
            return AppModel::select('id')->asc('id')->fetchColumn();
        });
    }

    /**
     * 根据域名查找应用编号
     *
     * @return string|null
     */
    protected function getIdByDomain(): ?string
    {
        $domain = $this->req->getHost();
        if (!$domain) {
            // CLI 下默认没有域名，直接返回
            return null;
        }

        if (in_array($domain, $this->domains, true)) {
            return null;
        }

        return $this->cache->remember('appDomain:' . $domain, 86400, static function () use ($domain) {
            $app = AppModel::select('id')->fetch('domain', $domain);
            return $app ? $app['id'] : null;
        });
    }

    /**
     * @internal
     */
    protected function matchPathInfo(string $pathInfo): ?array
    {
        $result = $this->pageRouter->match($pathInfo);
        if ($result) {
            return $result;
        }

        foreach ($this->pathMap as $search => $replace) {
            if (str_contains($pathInfo, $search)) {
                $pathInfo = str_replace($search, $replace, $pathInfo);
                $result = $this->pageRouter->match($pathInfo);
                if ($result) {
                    return $result;
                }
                break;
            }
        }

        if ($this->fallbackPathInfo) {
            return $this->pageRouter->match($this->fallbackPathInfo);
        }

        return null;
    }
}
