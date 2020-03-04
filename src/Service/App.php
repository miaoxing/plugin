<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Config\ConfigTrait;
use Miaoxing\Services\Service\Str;
use Wei\Response;

/**
 * 应用
 *
 * @property \Wei\Event $event
 * @property \Miaoxing\Plugin\Service\Plugin $plugin
 * @property \Miaoxing\Plugin\Service\AppRecord $appRecord 应用的数据库服务
 * @property Str $str
 */
class App extends \Wei\App
{
    use ConfigTrait;

    /**
     * 插件控制器不使用该格式,留空可减少类查找
     *
     * {@inheritdoc}
     */
    protected $controllerFormat = '';

    /**
     * 当前运行的插件名称
     *
     * @var string|false
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
     * @var \Miaoxing\Plugin\Service\AppRecord[]
     */
    protected $records = [];

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $options = [])
    {
        // TODO 调整到合适的位置
        wei()->laravel->bootstrap();

        $namespace = $this->getNamespace();
        wei()->setConfig('session:namespace', $namespace);

        $this->event->trigger('appInit');

        return $this->invokeApp($options);
    }

    protected function invokeApp(array $options = array())
    {
        $options && $this->setOption($options);

        // Parse the path info to parameter set
        $request = $this->request;
        $paramSet = $this->router->matchParamSet($request->getPathInfo(), $request->getMethod());

        // 当控制器不存在时，回退到该控制器，适用于前后端分离的情况
        $paramSet[] = [
            'controller' => $this->fallbackController,
            'action' => $this->fallbackAction,
        ];

        // Find out exiting controller action and execute
        $notFound = array();
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
     * {@inheritDoc}
     */
    protected function execute($instance, $action)
    {
        $app = $this;
        $wei = $this->wei;
        $middleware = $this->getMiddleware($instance, $action);

        $callback = function () use ($instance, $action, $app) {
            $instance->init();

            $instance->before($app->request, $app->response);

            $method = $app->getActionMethod($action);
            $response = $instance->$method($app->request, $app->response);

            $instance->after($app->request, $response);

            return $response;
        };

        $next = function () use (&$middleware, &$next, $callback, $wei, $instance) {
            $config = array_splice($middleware, 0, 1);
            if ($config) {
                $class = key($config);
                $service = new $class(array('wei' => $wei) + $config[$class]);
                $result = $service($next, $instance);
            } else {
                $result = $callback();
            }

            return $result;
        };

        return $this->handleResponse($next())->send();
    }

    public function getNamespace()
    {
        if (!$this->namespace) {
            $this->namespace = $this->detectNamespace();
        }

        return $this->namespace;
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
     * @return string|false
     */
    protected function getNamespaceFromDomain()
    {
        $domain = $this->request->getHost();
        if (!$domain || in_array($domain, $this->domains)) {
            return false;
        }

        return $this->appRecord->getIdByDomain($domain);
    }

    /**
     * Check if the namespace is available
     *
     * @param string $namespace
     * @return bool
     */
    public function isNamespaceAvailable($namespace)
    {
        return $this->appRecord->isExists($namespace);
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
     * 获取App数据表对象
     *
     * @return \Miaoxing\Plugin\Service\AppRecord
     */
    public function getRecord()
    {
        $namespace = $this->getNamespace();
        if (!isset($this->records[$namespace])) {
            $this->records[$namespace] = wei()->appRecord()
                ->tags(false)
                ->setCacheKey('appName:' . $namespace)
                ->cache(86400)
                ->find(['name' => $namespace]);
        }

        return $this->records[$namespace];
    }

    /**
     * Record: 获取当前项目的编号
     *
     * @return int
     * @throws \Exception
     */
    public function getId()
    {
        if (isset($this->ids[$this->namespace])) {
            return $this->ids[$this->namespace];
        } else {
            return (int) $this->getRecord()->get('id');
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
            $record = wei()->appRecord()->findById($id);
            $this->dbNames[$id] = $record['name'];
        }

        return $this->dbNames[$id];
    }

    /**
     * 重写handleResponse,支持Ret结构
     *
     * @param mixed $response
     * @return Response
     * @throws \Exception
     */
    public function handleResponse($response)
    {
        if ($this->isRet($response)) {
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
     * 转换Ret结构为response
     *
     * @param array $ret
     * @return Response
     * @throws \Exception
     */
    protected function handleRet(array $ret)
    {
        if ($this->request->acceptJson() || php_sapi_name() == 'cli' || $this->isApi()) {
            return $this->response->json($ret);
        } else {
            $type = isset($ret['retType']) ? $ret['retType'] : ($ret['code'] === 1 ? 'success' : 'warning');
            $content = $this->view->render('@plugin/ret/ret.php', $ret + ['type' => $type]);

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
     * 判断是否请求到后台页面
     *
     * @return bool
     */
    public function isAdmin()
    {
        // NOTE: 控制器不存在时，回退的控制器不带有 admin
        return strpos($this->request->getPathInfo(), '/admin') === 0;
    }

    /**
     * 判断是否为API接口
     *
     * @return bool
     */
    public function isApi()
    {
        return substr($this->getController(), 0, 3) === 'api';
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
}
