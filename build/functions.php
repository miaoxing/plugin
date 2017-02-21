<?php

function suc($message, $args = null, $args2 = null)
{
    $message = format(func_get_args());
    echo $message . PHP_EOL;

    return '';
}

/**
 * @SuppressWarnings(PHPMD)
 */
function err($message, $args = null, $args2 = null)
{
    $message = format(func_get_args());
    echo $message . PHP_EOL;

    $dir = 'reports';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $content = $message . str_repeat(PHP_EOL, 2) . str_repeat('=', 70);

    // 根据运行的脚本名生成错误文件名
    file_put_contents($dir . '/' . basename($_SERVER['SCRIPT_NAME'], '.php') . '.txt', $content);

    return '';
}

function format($args)
{
    $message = $args[0];
    if (isset($args[1])) {
        array_shift($args);
        $message = vsprintf($message, $args);
    }

    return PHP_EOL . $message . PHP_EOL;
}
