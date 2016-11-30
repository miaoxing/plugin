<?php

// @codingStandardsIgnoreFile

$file = 'coverage.txt';
$minPercentage = isset($argv[1]) ? (int) $argv[1] : 30;

if (!is_file($file)) {
    return err('未找到覆盖率文件"%s"', $file);
}

$content = file_get_contents($file);

preg_match('/Lines:\s+(.+?)%/', $content, $matches);
if (!isset($matches[1])) {
    return err('未匹配到覆盖率数据');
}

$percentage = $matches[1];
if ($percentage < $minPercentage) {
    return err('当前覆盖率(%s%%)过低,至少需要%s%%,请增加单元测试', $percentage, $minPercentage);
} else {
    return suc('覆盖率是%s%%,达到要求的%s%%', $percentage, $minPercentage);
}

function suc($message, $args = null, $args2 = null)
{
    $message = format(func_get_args());
    echo $message . PHP_EOL;

    return '';
}

function err($message, $args = null, $args2 = null)
{
    $message = format(func_get_args());
    echo $message . PHP_EOL;

    $dir = 'reports';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $content = $message . str_repeat(PHP_EOL, 2) . str_repeat('=', 70);
    file_put_contents($dir . '/check-coverage.txt', $content);

    return '';
}

function format($args)
{
    $message = $args[0];
    if (isset($args[1])) {
        array_shift($args);
        $message = vsprintf($message, $args);
    }

    return $message;
}
