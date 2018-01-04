<?php

namespace Miaoxing\Plugin\Service;

/**
 * 测试
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
     * @return $this
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
        $this->request = $request;

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
        $wei = $this->wei;

        // 1. 注入各种配置
        $wei->request->clear();
        $wei->request->set($this->request);
        $wei->request->setOption('gets', $this->query);
        $wei->request->setOption('posts', $this->post);
        $wei->request->setMethod($this->method);
        $wei->request->set('_format', $this->dataType);
        $wei->request->setContent($this->content);

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
        $wei->request->clear();
        $wei->request->setOption('gets', []);
        $wei->request->setOption('posts', []);
        $wei->request->setMethod('GET');
        $wei->request->set('_format', '');
        $wei->request->setContent(null);

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
        if ($this->response === null) {
            $this->exec();
        }

        return $this->response;
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
                $data = json_decode($data, $this->dataType === 'json');
                if (null === $data && json_last_error() != JSON_ERROR_NONE) {
                    $exception = new \ErrorException('JSON parsing error', json_last_error());
                }
                break;

            case 'xml':
            case 'serialize':
                $methods = [
                    'xml' => 'simplexml_load_string',
                    'serialize' => 'unserialize',
                ];
                // @codingStandardsIgnoreLine
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
     * Helper: 触发微信回复
     *
     * @param string $content
     * @return $this
     * @todo 转到微信相关服务
     */
    public function wechatReply($content)
    {
        $wei = wei();

        $this->initWechatApp($content);

        return $wei->tester()
            ->login()
            ->controller('wechat')
            ->action('reply')
            ->request(['accountId' => '768861673'])
            ->exec()
            ->response();
    }

    public function wechatDeviceReply($content)
    {
        $wei = wei();

        $this->initWechatApp($content);

        return $wei->tester()
            ->login()
            ->controller('wechatDeviceReplies')
            ->action('index')
            ->exec()
            ->response();
    }

    protected function initWechatApp($content)
    {
        $wei = wei();

        $account = wei()->wechatAccount->getCurrentAccount();
        $account->setData([
            'authed' => false,
            'applicationId' => 'wxbad0b45542aa0b5e',
            'token' => 'wei',
            'encodingAesKey' => 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFG',
        ]);

        // 模拟自定义回复
        $wei->weChatApp = new \Wei\WeChatApp([
            'wei' => $wei,
            'query' => [
                'signature' => '1273e279da5a3a236a8f20f40d18e5ecc7d84bc6',
                'timestamp' => '1366032735',
                'nonce' => '136587223',
            ],
            'postData' => $content,
        ]);
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
            ->request($data)
            ->json()
            ->exec()
            ->response();
    }
}
