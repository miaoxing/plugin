<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Config\Service\Config;
use Wei\Response;

/**
 * 应用
 *
 * @method \Miaoxing\Plugin\BaseModel appDb($table)
 * @property \Wei\Event $event
 * @property \Miaoxing\Plugin\Service\Plugin $plugin
 * @property \Miaoxing\Plugin\Service\AppRecord $appRecord 应用的数据库服务
 * @property Config $config
 * @property Str $str
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
     * @var string|false
     */
    protected $plugin = false;

    /**
     * @var \Miaoxing\Plugin\BaseModel
     */
    protected $record;

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
     * 应用的地址
     *
     * 如果是默认域名,则为应用名称,如果是自定义域名,则为空
     *
     * @var string
     */
    protected $appUrl = '';

    /**
     * 默认的应用首页,以便首页不是404
     *
     * @var string
     */
    protected $defaultNamespace = 'app';

    /**
     * @var string
     */
    protected $defaultViewFile = '@app/_default.php';

    /**
     * {@inheritdoc}
     */
    public function __invoke(array $options = [])
    {
        // 1. 获取应用名称和请求路径
        $namespace = $this->getNamespaceFromDomain();

        if (!$namespace) {
            $request = $this->request;
            $pathInfo = $request->getPathInfo();
            if (isset($request['app'])) {
                $namespace = $request['app'];
            } else {
                $parts = explode('/', $pathInfo, 3);
                list(, $namespace, $pathInfo) = $parts;

                // FIXME 临时映射
                if ($namespace === 'ald') {
                    $namespace = 'pas';
                }

                // 可能是微信第三方平台的通知,转为应用名称
                $wechatNamespace = $this->getNamespaceFromWechatAppId($namespace);
                if ($wechatNamespace) {
                    $namespace = $wechatNamespace;
                }

                $request->setPathInfo('/' . $pathInfo);
            }

            if (!$namespace) {
                $namespace = $this->defaultNamespace;
            }

            // 2. 检查应用名称是否存在
            if (!$this->isNamespaceAvailable($namespace)) {
                throw new \Exception('应用不存在', 404);
            }
            $request->setBaseUrl('/' . $namespace);
            $this->appUrl = '/' . $namespace;
        }
        $this->setNamespace($namespace);

        // 3. 设置namespace,让后面初始化的服务,都注入namespace
        $wei = $this->wei;
        $wei->setNamespace($namespace);
        $wei->setConfig('session:namespace', $namespace);
        $wei->db->setOption('dbname', $namespace);

        $this->event->trigger('appInit');

        // 4. 启用URL映射
        wei()->urlMapper();

        return parent::__invoke($options);
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
     * 通过微信AppID获取应用的名称
     *
     * @param string $namespace
     * @return string|false
     */
    protected function getNamespaceFromWechatAppId($namespace)
    {
        if (substr($namespace, 0, 2) != 'wx') {
            return false;
        }

        wei()->logger->debug('Got wechat appId', ['appId' => $namespace]);

        $appId = wei()->wechatAccount()->select('appId')->fetchColumn(['applicationId' => $namespace]);
        if (!$appId) {
            return false;
        }

        $namespace = wei()->appRecord()->select('name')->fetchColumn(['id' => $appId]);
        wei()->logger->debug('Convert wechat appId to namespace', ['namespace' => $namespace]);

        return $namespace;
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
        $this->record || $this->record = wei()->appRecord()
            ->tags(false)
            ->setCacheKey('appName:' . $this->namespace)
            ->cache(86400)
            ->find(['name' => $this->namespace]);

        return $this->record;
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
     * @return array
     */
    public function getConfig()
    {
        return [
            'baseUrl' => $this->request->getBaseUrl(),
            'appUrl' => $this->appUrl,
        ];
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
        if ($this->request->acceptJson() || php_sapi_name() == 'cli') {
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

    public function isAdmin()
    {
        return substr($this->getController(), 0, 5) === 'admin';
    }
}
