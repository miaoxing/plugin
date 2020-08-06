<?php

namespace Miaoxing\Plugin\Service;

/**
 * 测试
 *
 * @mixin \ReqMixin
 */
class Tester extends \Miaoxing\Plugin\BaseService
{
    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $action = 'index';

    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var array
     */
    protected $request = [];

    /**
     * @var array
     */
    protected $post = [];

    /**
     * @var array
     */
    protected $query = [];

    /**
     * @var array
     */
    protected $session = [];

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $dataType;

    /**
     * @var mixed
     */
    protected $response;

    /**
     * @param string|null $controller
     * @param string|null $action
     * @return static
     */
    public function __invoke($controller = null, $action = null)
    {
        return new static([
            'wei' => $this->wei,
            'controller' => $controller,
            'action' => $action ?: $this->action,
        ]);
    }

    /**
     * @param string $controller
     * @return $this
     */
    public function controller($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * @param string $action
     * @return $this
     */
    public function action($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function method($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @param array $request
     * @return $this
     */
    public function request(array $request)
    {
        $this->req = $request;

        return $this;
    }

    /**
     * @param array $query
     * @return $this
     */
    public function query(array $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param array $post
     * @return $this
     */
    public function post(array $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @param array $session
     * @return $this
     */
    public function session(array $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string $dataType
     * @return $this
     */
    public function dataType($dataType)
    {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * @return $this
     */
    public function json()
    {
        return $this->dataType('json');
    }

    /**
     * 调用指定的控制器盒方法
     *
     * @return $this
     * @throws \Exception
     */
    public function exec()
    {
        if (!$this->controller) {
            $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            if (isset($traces[1]['class']) && __CLASS__ !== $traces[1]['class']) {
                $match = $traces[1];
            } else {
                $match = $traces[2];
            }
            $this->controller = $this->getControllerByClass($match['class']);
            $this->action = $this->getActionByMethod($match['function']);
        }

        $wei = $this->wei;

        // 1. 注入各种配置
        $wei->req->clear();
        $wei->req->set($this->req);
        $wei->req->setOption('gets', $this->query);
        $wei->req->setOption('posts', $this->post);
        $wei->req->setMethod($this->method);
        $wei->req->set('_format', $this->dataType);
        $wei->req->setContent($this->content);

        $wei->session->set($this->session);

        // 2. 调用相应的URL
        ob_start();
        try {
            $res = $wei->app->dispatch($this->controller, $this->action);
            $this->response = $this->parseResponse($res->getContent(), $exception);
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
        ob_end_clean();

        // 3. 还原原来的配置
        $wei->req->clear();
        $wei->req->setOption('gets', []);
        $wei->req->setOption('posts', []);
        $wei->req->setMethod('GET');
        $wei->req->set('_format', '');
        $wei->req->setContent('');

        foreach ($this->session as $key => $value) {
            $wei->session->remove($key);
        }

        return $this;
    }

    /**
     * @return string|array
     * @throws \Exception
     */
    public function response()
    {
        if (null === $this->response) {
            $this->exec();
        }

        return $this->response;
    }

    /**
     * Helper: 设置登录用户
     *
     * @param int $userId
     * @return $this
     */
    public function login($userId = 1)
    {
        return $this->session(['user' => ['id' => $userId]]);
    }

    /**
     * Helper: 销毁用户登录状态
     *
     * @return $this
     */
    public function logout()
    {
        unset($this->session['user']);

        return $this;
    }

    /**
     * 运行任务
     *
     * @param string $name
     * @param array $data
     * @return array
     */
    public function runJob($name, array $data = [])
    {
        $parts = explode('/', $name);
        $action = array_pop($parts);
        $controller = implode('/', $parts);

        return wei()->tester()
            ->login(1)
            ->controller($controller)
            ->action($action)
            ->req($data)
            ->json()
            ->exec()
            ->response();
    }

    public function req($data = [])
    {
        if (!User::isLogin()) {
            $this->login();
        }

        return $this->req($data)
            ->json()
            ->exec()
            ->response();
    }

    protected function getControllerByClass($class)
    {
        $parts = explode('Controller\\', $class);
        $controller = substr($parts[1], 0, -4);
        return implode('/', array_map('lcfirst', explode('\\', $controller)));
    }

    protected function getActionByMethod($method)
    {
        preg_match('/^test(.+?)Action/', $method, $match);
        if (isset($match[1])) {
            return lcfirst($match[1]);
        }

        return null;
    }

    /**
     * Parse response data by specified type
     *
     * @param string $data
     * @param null $exception A variable to store exception when parsing error
     * @return mixed
     */
    protected function parseResponse($data, &$exception)
    {
        switch ($this->dataType) {
            case 'json':
            case 'jsonObject':
                $data = json_decode($data, 'json' === $this->dataType);
                if (null === $data && JSON_ERROR_NONE != json_last_error()) {
                    $exception = new \ErrorException('JSON parsing error', json_last_error());
                }
                break;

            case 'xml':
            case 'serialize':
                $methods = [
                    'xml' => 'simplexml_load_string',
                    'serialize' => 'unserialize',
                ];
                // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
                $data = @$methods[$this->dataType]($data);
                if (false === $data && $e = error_get_last()) {
                    $exception = new \ErrorException($e['message'], $e['type'], 0, $e['file'], $e['line']);
                }
                break;

            case 'query':
                // Parse $data(string) and assign the result to $data(array)
                parse_str($data, $data);
                break;

            case 'text':
            default:
                break;
        }

        return $data;
    }
}
