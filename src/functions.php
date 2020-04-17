<?php

if (!function_exists('suc')) {
    /**
     * Return operation successful result
     *
     * @param string $message
     * @return array
     */
    function suc($message = null)
    {
        return wei()->ret->suc(...func_get_args());
    }
}

if (!function_exists('err')) {
    /**
     * Return operation failed result, and logs with an info level
     *
     * @param string|array $message
     * @param int $code
     * @param string $level
     * @return array
     */
    function err($message, $code = -1, $level = 'info')
    {
        return wei()->ret->err(...func_get_args());
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
        return wei()->ret(...func_get_args());
    }
}
