<?php

namespace Miaoxing\Plugin\Service;

use Wei\Base;

/**
 * @mixin \LoggerMixin
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
        return $this->__invoke($message, $code, $level);
    }

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
            !isset($rawMessage) && $rawMessage = $data['message'];
            !isset($params) && $params = $data;
            $this->logger->log($type, $rawMessage, $params);
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
}
