<?php

namespace Miaoxing\Plugin\Callback;

abstract class HttpCallback
{
    public static $lastErrorHttp = null;

    /**
     * @param $response
     * @param \Wei\Http $http
     */
    public static function success($response, \Wei\Http $http)
    {
        $retries = $http->getOption('retries');
        $leftRetries = $http->getOption('leftRetries');

        if ($retries && $retries != $leftRetries) {
            // 上报是第几次重试才成功
            $curRetry = $retries - $leftRetries;
            wei()->statsD->increment('http.retries.' . $curRetry . '.success');
        }
    }

    /**
     * cURL请求发送失败的回调,记录错误原因和异常堆栈
     *
     * @param \Wei\Http $http
     */
    public static function error(\Wei\Http $http)
    {
        // 只有重试了仍然错误才记录日志
        if ($http->getOption('leftRetries')) {
            return;
        }

        $level = $http->getOption('errorLevel') ?: 'error';

        $response = $http->getResponseText();
        if (mb_strlen($response) > 1024) {
            $body = mb_substr($response, 0, 1024) . '...';
        } else {
            $body = $response;
        }

        $exception = $http->getErrorException();
        wei()->logger->log($level, $exception->getMessage(), [
            'url' => $http->getUrl(),
            'method' => $http->getMethod(),
            'data' => $http->getData(),
            'curlInfo' => $http->getCurlInfo(),
            'curlOptions' => $http->getOption('curlOptions'),
            'responseBody' => $body,
            'code' => $exception->getCode(),
            'exception' => (string) $exception,
        ]);

        static::$lastErrorHttp = $http;
    }
}
