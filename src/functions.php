<?php

use Miaoxing\Plugin\Ret;

if (!function_exists('suc')) {
    /**
     * Return operation successful result
     *
     * @param string $message
     * @return Ret
     */
    function suc($message = null)
    {
        return Ret::createSuc($message);
    }
}

if (!function_exists('err')) {
    /**
     * Return operation failed result, and logs with an info level
     *
     * @param string|array $message
     * @param int $code
     * @param string $level
     * @return Ret
     */
    function err($message, $code = -1, $level = 'info')
    {
        return Ret::createErr(...func_get_args());
    }
}

if (!function_exists('ret')) {
    /**
     * Return operation result data
     *
     * @param string|array $message
     * @param int $code
     * @param string $type
     * @return mixed|string
     */
    function ret($message, $code = 1, $type = 'success')
    {
        return new Ret(...func_get_args());
    }
}
