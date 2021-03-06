<?php

namespace Miaoxing\Plugin\Callback;

abstract class DbCallback
{
    public static function beforeQuery($sql, $params)
    {
        $message = $sql;
        if ($params) {
            $message .= ' with parameters: ' . json_encode($params, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
        }
        wei()->logger->debug($message);
    }
}
