<?php

namespace Miaoxing\Plugin;

class Ret implements \JsonSerializable, \ArrayAccess
{
    /**
     * The default operation result data
     *
     * @var array
     */
    protected $defaults = [
        'message' => '操作成功',
        'code' => 1,
    ];

    protected $data = [];

    public function __construct($message, $code = 1, string $type = 'success')
    {
        if (is_array($message)) {
            $this->data = $message + ['code' => $code] + $this->defaults;
        } else {
            $this->data = ['message' => (string)$message, 'code' => $code];
        }

        // Record error result
        // TODO record more relative data
        if ($code !== 1) {
            //$this->logger->log($type, $data['message'], $data);
        }
    }

    public static function createSuc($message = null)
    {
        return new static($message);
    }

    public static function createErr($message, $code = 0, $level = 'info')
    {
        return new static ($message, $code, $level);
    }

    /**
     * @return bool
     */
    public function suc()
    {
        return $this->data['code'] === 1;
    }

    /**
     * @return bool
     */
    public function err()
    {
        return $this->data['code'] !== 1;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
