<?php

namespace Miaoxing\Plugin\Service;

use Exception;
use JsonSerializable;
use Miaoxing\Plugin\BaseController;
use Miaoxing\Plugin\RetException;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
use Wei\Response;

/**
 * 应用
 *
 * @mixin \EventMixin
 * @mixin \StrMixin
 * @mixin \AppModelMixin
 * @mixin \CacheMixin
 */
class App extends \Wei\App
{
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
     * @var array
     */
    protected $ids = [];

    /**
     * @var array
     */
    protected $dbNames = [];

    /**
     * 默认域名
     *
     * 如果请求的默认域名,就不到数据库查找域名
     *
     * @var array
     */
    protected $domains = [];

    /**
     * 预先定义的应用slug,可以减少查询
     *
     * @var array
     */
    protected $predefinedNames = ['app'];

    /**
     * 默认的应用首页,以便首页不是404
     *
     * @var string
     */
    protected $defaultNamespace = 'app';

    /**
     * @var string
     */
    protected $defaultViewFile = '@plugin/_default.php';

    /**
     * 当页面不存在时，加载该控制器
     *
     * @var string
     */
    protected $fallbackController = 'app';

    /**
     * 当页面不存在时，加载该方法
     *
     * @var string
     */
    protected $fallbackAction = 'index';

    /**
     * @var \Miaoxing\Plugin\Service\AppModel[]
     */
    protected $records = [];

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $options = [])
    {
        $this->event->trigger('appInit');

        return $this->invokeApp($options);
    }

    public function getNamespace()
    {
        if (!$this->namespace) {
            $this->namespace = $this->detectNamespace();
        }

        return $this->namespace;
    }

    /**
     * Check if the namespace is available
     *
     * @param string $namespace
     * @return bool
     */
    public function isNamespaceAvailable($namespace)
    {
        // 忽略非数字和字母组成的项目名称
        if (!ctype_alnum($namespace)) {
            return false;
        }

        if (in_array($namespace, $this->predefinedNames, true)) {
            return true;
        }

        return $this->cache->get('appExists:' . $namespace, 86400, function () use ($namespace) {
            $app = wei()->appModel()->select('name')->fetch('name', $namespace);

            return $app && $app['name'] === $namespace;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultTemplate($controller = null, $action = null)
    {
        $file = lcfirst($controller ?: $this->controller) . '/' . ($action ?: $this->action)
            . $this->view->getExtension();

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
        if (!$this->plugin) {
            $classes = array_reverse($this->getControllerClasses($this->controller));
            foreach ($classes as $class) {
                // 认为第二部分是插件名称
                list(, $plugin) = explode('\\', $class, 3);
                $this->plugin = $this->str->dash($plugin);

                break;
            }
        }

        return $this->plugin;
    }

    /**
     * 获取当前应用模型对象
     *
     * @return \Miaoxing\Plugin\Service\AppModel
     */
    public function getModel()
    {
        $namespace = $this->getNamespace();
        if (!isset($this->records[$namespace])) {
            $this->records[$namespace] = wei()->appModel()
                ->tags(false)
                ->setCacheKey('appName:' . $namespace)
                ->cache(86400)
                ->findBy('name', $namespace);
        }

        return $this->records[$namespace];
    }

    /**
     * 设置当前应用模型对象
     *
     * @param AppModel $model
     * @return $this
     */
    public function setModel(AppModel $model)
    {
        $this->records[$this->getNamespace()] = $model;
        return $this;
    }

    /**
     * Record: 获取当前项目的编号
     *
     * @return int
     * @throws Exception
     */
    public function getId()
    {
        $namespace = $this->getNamespace();
        if (isset($this->ids[$namespace])) {
            return $this->ids[$namespace];
        } else {
            return (int) $this->getModel()->get('id');
        }
    }

    /**
     * Repo: 根据应用ID获取应用数据库名称
     *
     * @param int $id
     * @return string
     */
    public function getDbName($id)
    {
        if (!$this->dbNames[$id]) {
            $record = wei()->appModel()->find($id);
            $this->dbNames[$id] = $record['name'];
        }

        return $this->dbNames[$id];
    }

    /**
     * 重写handleResponse,支持Ret结构
     *
     * @param mixed $response
     * @return Response
     * @throws Exception
     */
    public function handleResponse($response)
    {
        if ($response instanceof JsonSerializable || $this->isRet($response)) {
            return $this->handleRet($response);
        } elseif (is_array($response)) {
            $template = $this->getDefaultTemplate();
            $file = $this->view->resolveFile($template) ? $template : $this->defaultViewFile;
            $content = $this->view->render($file, $response);
            return $this->response->setContent($content);
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
        return 0 === strpos($this->request->getRouterPathInfo(), '/admin');
    }

    /**
     * 判断是否为API接口
     *
     * @return bool
     */
    public function isApi()
    {
        return 0 === strpos($this->getController(), 'api') || 0 === strpos($this->getController(), 'adminApi');
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

    protected function invokeApp(array $options = [])
    {
        $options && $this->setOption($options);

        // Parse the path info to parameter set
        $request = $this->request;
        $paramSet = $this->router->matchParamSet($request->getRouterPathInfo(), $request->getMethod());

        // TODO router 负责转换为 camel?
        foreach ($paramSet as $i => $params) {
            $paramSet[$i]['controller'] = $this->str->camel($params['controller']);
        }

        // 当控制器不存在时，回退到该控制器，适用于前后端分离的情况
        $paramSet[] = [
            'controller' => $this->fallbackController,
            'action' => $this->fallbackAction,
        ];

        // Find out exiting controller action and execute
        $notFound = [];
        foreach ($paramSet as $params) {
            $response = $this->dispatch($params['controller'], $params['action'], $params, false);
            if (is_array($response)) {
                $notFound = array_merge($notFound, $response);
            } else {
                return $response;
            }
        }
        throw $this->buildException($notFound);
    }


    /**
     * @param BaseController $instance
     * @param string $action
     * @return Response
     * @throws Exception
     */
    protected function execute($instance, $action)
    {
        $wei = $this->wei;

        $instance->init();
        $middleware = $this->getMiddleware($instance, $action);

        $callback = function () use ($instance, $action) {
            $instance->before($this->request, $this->response);

            $method = $this->getActionMethod($action);
            // TODO 和 forward 异常合并一起处理
            try {
                $args = $this->buildActionArgs($instance, $method);
                $response = $instance->{$method}(...$args);
            } catch (RetException $e) {
                return $e->getRet();
            }

            $instance->after($this->request, $response);

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
            return [$this->request, $this->response];
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
        // @link https://github.com/phpstan/phpstan/issues/1133
        /** @var \ReflectionNamedType|null $type */
        $type = $param->getType();

        // Handle Model class
        if ($type
            && !$type->isBuiltin()
            && is_a($type->getName(), Model::class, true)
        ) {
            return $type->getName()::findOrFail($this->request['id']);
        }

        // Handle other class
        if ($type && !$type->isBuiltin()) {
            throw new Exception('Unsupported action parameter type: ' . $type);
        }

        // TODO Throw exception for unsupported builtin type
        // Handle builtin type
        $arg = $this->request[$param->getName()];
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

    protected function detectNamespace()
    {
        // 1. 域名
        if ($namespace = $this->getNamespaceFromDomain()) {
            return $namespace;
        }

        // 2. 请求参数
        if ($namespace = parent::getNamespace()) {
            return $namespace;
        }

        // 3. 默认
        return $this->defaultNamespace;
    }

    /**
     * @return false|string
     */
    protected function getNamespaceFromDomain()
    {
        $domain = $this->request->getHost();
        if (!$domain || in_array($domain, $this->domains, true)) {
            return false;
        }

        return $this->getIdByDomain($domain);
    }

    /**
     * 转换Ret结构为response
     *
     * @param array|Ret $ret
     * @return Response
     * @throws Exception
     */
    protected function handleRet($ret)
    {
        if ($this->request->acceptJson() || $this->isApi()) {
            return $this->response->json($ret);
        } else {
            if ($ret instanceof Ret) {
                $ret = $ret->toArray();
            }
            $type = $ret['retType'] ?? (1 === $ret['code'] ? 'success' : 'warning');
            $content = $this->view->render('@plugin/_ret.php', $ret + ['type' => $type]);

            return $this->response->setContent($content);
        }
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
     * 根据域名查找应用名称
     *
     * @param string $domain
     * @return false|string
     * @svc
     */
    protected function getIdByDomain($domain)
    {
        return $this->cache->get('appDomain:' . $domain, 86400, static function () use ($domain) {
            $app = AppModel::select('name')->fetch('domain', $domain);

            return $app ? $app['name'] : false;
        });
    }
}
