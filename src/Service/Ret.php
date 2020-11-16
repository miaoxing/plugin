<?php

namespace Miaoxing\Plugin\Service;

use Wei\Base;
use Wei\Req;
use Wei\Res;

/**
 * @mixin \LoggerMixin
 * @mixin \PluginMixin
 * @mixin \EnvMixin
 * @mixin \ReqMixin
 * @mixin \ResMixin
 * @mixin \ViewMixin
 */
class Ret extends Base implements \JsonSerializable, \ArrayAccess
{
    /**
     * @var bool
     */
    protected static $createNewInstance = true;

    /**
     * The default operation result data
     *
     * @var array
     */
    protected $defaults = [
        'message' => 'Operation successful',
        'code' => 1,
    ];

    /**
     * The current operation result data
     *
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @var array
     */
    private static $errors = [];

    /**
     * Return operation result data
     *
     * @param array|string $message
     * @param int $code
     * @param string $type
     * @return $this
     */
    public function __invoke($message, $code = 1, $type = 'success')
    {
        if (1 !== $code && 1 === func_num_args()) {
            $code = $this->generateCode($message);
        }

        if (is_string($message)) {
            // 直接传入消息字符串
            // $this->xxx('message', code);
            $data = ['message' => $message, 'code' => $code];
        } elseif (is_array($message) && !isset($message[0])) {
            // 通过索引数组传入更多参数
            // $this->>xxx(['message' => 'xxx', 'code' => xxx, 'more' => 'xxx']);
            $data = $message + ['code' => $code] + $this->defaults;
        } else {
            // 通过关联数组传入原始消息和参数，可记录原始消息到日志中，方便合并相同日志
            // $this->>xxx(['me%sage', 'ss'], code)
            $rawMessage = array_shift($message);
            $params = $message;
            $message = vsprintf($rawMessage, $params);
            $data = ['message' => (string) $message, 'code' => $code];
        }
        $this->data = $data;

        // Record error result
        if (1 !== $code) {
            $this->logger->log($type, $rawMessage ?? $data['message'], $params ?? $data);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuc()
    {
        return !$this->isErr();
    }

    /**
     * @return bool
     */
    public function isErr()
    {
        return 1 !== $this->data['code'];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Returns metadata value by key, or returns all metadata if key is not set
     *
     * @param string|null $key
     * @return array|mixed|null
     */
    public function getMetadata(string $key = null)
    {
        return null === $key ? $this->metadata : ($this->metadata[$key] ?? null);
    }

    /**
     * Sets metadata by key or sets all metadata if key is an array
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setMetadata($key, $value = null)
    {
        if (is_array($key)) {
            $this->metadata = $key;
        } else {
            $this->metadata[$key] = $value;
        }
        return $this;
    }

    /**
     * Removes metadata value by key or clears all metadata if key is not set
     *
     * @param string $key
     * @return $this
     */
    public function removeMetadata(string $key = null)
    {
        if (null === $key) {
            $this->metadata = [];
        } else {
            unset($this->metadata[$key]);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Convert Ret object to response
     *
     * @param Req|null $req
     * @param Res|null $res
     * @return Res
     * @throws \Exception
     */
    public function toRes(Req $req = null, Res $res = null)
    {
        $req || $req = $this->req;
        $res || $res = $this->res;

        $model = $this->getMetadata('model');
        if ($model instanceof Model && $model->wasRecentlyCreated()) {
            $res->setStatusCode(201);
        }

        if ($req->acceptJson() || $this->isApi($req)) {
            return $res->json($this);
        } else {
            $type = $this->data['retType'] ?? (1 === $this->isSuc() ? 'success' : 'warning');
            $content = $this->view->render('@plugin/_ret.php', $this->data + ['type' => $type]);
            return $res->setContent($content);
        }
    }

    /**
     * Return operation successful result
     *
     * ```php
     * // Specified message
     * $this->suc('Payment successful');
     *
     * // Format
     * $this->suc(['me%sage', 'ss']);
     *
     * // More data
     * $this->suc(['message' => 'Read successful', 'page' => 1, 'rows' => 123]);
     * ```
     *
     * @param array|string|null $message
     * @return $this
     * @svc
     */
    protected function suc($message = null)
    {
        return $this->__invoke($message ?: $this->defaults['message'], 1, 'success');
    }

    /**
     * Return operation failed result, and logs with an info level
     *
     * @param array|string $message
     * @param int $code
     * @param string $level
     * @return $this
     * @svc
     */
    protected function err($message, $code = 0, $level = 'info')
    {
        if (1 === func_num_args() && $this->env->isDev()) {
            $code = $this->generateCode($message);
        }

        return $this->__invoke($message, $code, $level);
    }

    /**
     * @param string|array $message
     * @return int
     * @throws \Exception
     */
    private function generateCode($message)
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 4);

        if ('err' === $traces[3]['function']) {
            // err();
            $file = $traces[3]['file'];
        } else {
            // Ret:err();
            $file = $traces[2]['file'];
        }

        $name = explode('/plugins/', $file)[1];
        $name = explode('/', $name, 2)[0];
        $plugin = $this->plugin->getOneById($name);

        $errors = $this->getErrors($name);
        $key = is_array($message) ? ($message['message'] ?? $message[0]) : $message;
        if (isset($errors[$key])) {
            $code = $errors[$key];
        } else {
            $lastCode = end($errors);
            $code = $lastCode ? $lastCode + 1 : $plugin->getCode() * 1000;
            $errors[$key] = $code;
            $this->setErrors($name, $errors);
        }

        return $code;
    }

    private function getErrors($name)
    {
        if (!isset(static::$errors[$name])) {
            $file = $this->getErrorFile($name);
            if (is_file($file)) {
                static::$errors[$name] = require $file;
            } else {
                static::$errors[$name] = [];
            }
        }
        return static::$errors[$name];
    }

    private function setErrors($name, $errors)
    {
        static::$errors[$name] = $errors;
        $file = $this->getErrorFile($name);
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        // Convert to short array syntax
        $content = "<?php\n\nreturn [" . substr(var_export($errors, true), strlen('array ('), -1) . "];\n";
        file_put_contents($this->getErrorFile($name), $content);
    }

    private function getErrorFile($name)
    {
        return 'plugins/' . $name . '/config/errors.php';
    }

    /**
     * 判断是否为API接口
     *
     * @param Req $req
     * @return bool
     */
    private function isApi(Req $req)
    {
        $pathInfo = $req->getRouterPathInfo();
        return 0 === strpos($pathInfo, '/api') || 0 === strpos($pathInfo, '/admin-api');
    }
}
