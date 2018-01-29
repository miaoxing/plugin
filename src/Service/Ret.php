<?php

namespace Miaoxing\Plugin\Service;

class Ret extends \Wei\Ret
{
    /**
     * {@inheritdoc}
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
            // $this->>xxx(['me%age', 'ss'], code)
            $rawMessage = array_shift($message);
            $params = $message;
            $message = vsprintf($rawMessage, $params);
            $data = ['message' => (string) $message, 'code' => $code];
        }

        // Record error result
        if ($code !== 1) {
            !isset($rawMessage) && $rawMessage = $data['message'];
            !isset($params) && $params = $data;
            $this->logger->log($type, $rawMessage, $params);
        }

        return $data;
    }
}
