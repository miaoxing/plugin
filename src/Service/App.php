<?php

namespace Miaoxing\Plugin\Service;

use Exception;
use JsonSerializable;
use Miaoxing\Plugin\BaseController;
use Miaoxing\Plugin\RetException;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Wei\Res;

/**
 * 应用
 *
 * @mixin \EventMixin
 * @mixin \StrMixin
 * @mixin \AppModelMixin
 * @mixin \CacheMixin
 * @mixin \PageRouterMixin
 */
class App extends \Wei\App
{
    protected const METHOD_NOT_ALLOWED = 405;

    /**
     * 插件控制器不使用该格式,留空可减少类查找
     *
     * {@inheritdoc}
     */
    protected $controllerFormat = '';

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
     * The default id of the current application
     *
     * @var int
     */
    protected $defaultId = 1;

    /**
     * @var string
     */
    protected $defaultViewFile = '@plugin/_default.php';

    /**
     * @var string
     */
    protected $fallbackPathInfo = 'app';

    /**
     * The id of the current application
     *
     * @var int
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
     */
    private $page = [
        'file' => '',
    ];

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $options = [])
    {
        $this->event->trigger('appInit');

        return $this->invokeApp($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTemplate($controller = null, $action = null)
    {
        $file = $controller ?: $this->page['file'];
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
     * 获取当前控制器下的视图文件,即可省略当前插件和控制器名称不写
     *
     * @param string $action
     * @return string
     * @deprecated
     */
    public function getControllerFile($action)
    {
        return $this->view->getFile($this->getDefaultTemplate(null, $action));
    }

    /**
     * 获取当前运行的插件名称
     *
     * @return string
     */
    public function getPlugin()
    {
        if (!$this->plugin && $this->page['file']) {
            // 认为第二部分是插件名称
            list(, $plugin) = explode('/', $this->page['file'], 3);
            $this->plugin = $plugin;
        }
        return $this->plugin;
    }

    /**
     * Return the current application model object
     *
     * @return AppModel
     * @throws Exception When the application not found
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
     * @param AppModel $model
     * @return $this
     */
    public function setModel(AppModel $model): self
    {
        $this->models[$this->getId()] = $model;
        return $this;
    }

    /**
     * Set the id of the current application
     *
     * @param int|null $id
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Return the id of the current application
     *
     * @return int
     */
    public function getId(): int
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
     * @throws Exception
     */
    public function handleResponse($response)
    {
        if ($response instanceof Ret || $this->isRet($response)) {
            return $this->handleRet($response);
        } elseif ($response instanceof JsonSerializable) {
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
        return 0 === strpos($this->req->getRouterPathInfo(), '/admin');
    }

    /**
     * 判断是否为API接口
     *
     * @return bool
     * @deprecated
     */
    public function isApi()
    {
        $pathInfo = $this->req->getRouterPathInfo();
        return 0 === strpos($pathInfo, '/api') || 0 === strpos($pathInfo, '/admin-api');
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
     * Returns the method name of specified acion
     *
     * @param string $action
     * @return string
     */
    public function getActionMethod($action)
    {
        return $action;
    }

    protected function invokeApp(array $options = [])
    {
        $options && $this->setOption($options);

        $pathInfo = $this->req->getRouterPathInfo();
        $result = $this->pageRouter->match($pathInfo);
        if (!$result) {
            $result = $this->pageRouter->match($this->fallbackPathInfo);
        }

        $this->req->set($result['params']);
        $page = require $result['file'];

        $this->page = [
            'file' => $result['file'],
            'page' => $page,
        ];

        $method = $this->req->getMethod();
        if (!method_exists($page, $method)) {
            $this->res->setStatusCode(static::METHOD_NOT_ALLOWED);
            throw new \Exception('Method Not Allowed', static::METHOD_NOT_ALLOWED);
        }

        return $this->execute($page, $method);
    }

    /**
     * @param BaseController $instance
     * @param string $action
     * @return Res
     * @throws Exception
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
                $args = $this->buildActionArgs($instance, $method);
                $response = $instance->{$method}(...$args);
            } catch (RetException $e) {
                return $e->getRet();
            }

            $instance->after($this->req, $response);

            return $response;
        };

        $next = function () use (&$middleware, &$next, $callback, $wei, $instance) {
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
     * @param object $instance
     * @param string $method
     * @return array
     * @throws ReflectionException
     */
    protected function buildActionArgs($instance, string $method)
    {
        $ref = new ReflectionMethod($instance, $method);
        $params = $ref->getParameters();
        if (!$params || 'req' === $params[0]->getName()) {
            return [$this->req, $this->res];
        }

        $args = [];
        foreach ($params as $param) {
            $args[] = $this->buildActionArg($param);
        }
        return $args;
    }

    /**
     * @param ReflectionParameter $param
     * @return mixed
     * @throws ReflectionException
     */
    protected function buildActionArg(ReflectionParameter $param)
    {
        /** @link https://github.com/phpstan/phpstan/issues/1133 */
        /** @var \ReflectionNamedType|null $type */
        $type = $param->getType();

        // Handle Model class
        if ($type
            && !$type->isBuiltin()
            && is_a($type->getName(), WeiBaseModel::class, true)
        ) {
            return $type->getName()::findOrFail($this->req['id']);
        }

        // Handle other class
        if ($type && !$type->isBuiltin()) {
            throw new Exception('Unsupported action parameter type: ' . $type);
        }

        // TODO Throw exception for unsupported builtin type
        // Handle builtin type
        $arg = $this->req[$param->getName()];
        if (null === $arg) {
            if ($param->isDefaultValueAvailable()) {
                $arg = $param->getDefaultValue();
            } else {
                throw new Exception('Missing required parameter: ' . $param->getName(), 400);
            }
        } elseif ($type) {
            settype($arg, $type->getName());
        }

        return $arg;
    }

    /**
     * 转换Ret结构为response
     *
     * @param array|Ret $ret
     * @return Res
     * @throws Exception
     */
    protected function handleRet($ret)
    {
        if (is_array($ret)) {
            if (1 === $ret['code']) {
                $ret = Ret::suc($ret);
            } else {
                $ret = Ret::err($ret);
            }
        }
        return $ret->toRes($this->req, $this->res);
    }

    /**
     * 检查是否返回了Ret结构
     *
     * @param mixed $response
     * @return bool
     */
    protected function isRet($response)
    {
        return is_array($response)
            && array_key_exists('code', $response)
            && array_key_exists('message', $response);
    }

    /**
     * Detect the id of application
     *
     * @return int
     */
    protected function detectId(): int
    {
        // 1. Domain
        if ($id = $this->getIdByDomain()) {
            return $id;
        }

        // 2. Request parameter
        if ($id = (int) $this->req->get('appId')) {
            return $id;
        }

        // 3. Default
        return $this->defaultId;
    }

    /**
     * 根据域名查找应用名称
     *
     * @return string|null
     */
    protected function getIdByDomain(): ?string
    {
        $domain = $this->req->getHost();
        if (!$domain || in_array($domain, $this->domains, true)) {
            return null;
        }

        return $this->cache->get('appDomain:' . $domain, 86400, static function () use ($domain) {
            $app = AppModel::select('id')->fetch('domain', $domain);
            return $app ? (int) $app['id'] : null;
        });
    }
}
