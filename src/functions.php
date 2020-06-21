<?php

use Miaoxing\Plugin\Service\Ret;

if (!function_exists('suc')) {
    /**
     * Return operation successful result
     *
     * @param string|array|null $message
     * @return Ret
     */
    function suc($message = null)
    {
        return Ret::suc($message);
    }
}

if (!function_exists('err')) {
    /**
     * Return operation failed result, and logs with an info level
     *
     * @param array|string $message
     * @param int $code
     * @param string $level
     * @return Ret
     */
    function err($message, $code = -1, $level = 'info')
    {
        return Ret::err(...func_get_args());
    }
}

if (!function_exists('ret')) {
    /**
     * Return operation result data
     *
     * @param array|string $message
     * @param int $code
     * @param string $type
     * @return mixed|string
     */
    function ret($message, $code = 1, $type = 'success')
    {
        return wei()->ret->__invoke(...func_get_args());
    }
}

if (!function_exists('req')) {
    function req($name = null)
    {
        return null === $name ? wei()->request : wei()->request[$name];
    }
}
