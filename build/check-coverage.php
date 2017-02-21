<?php

// @codingStandardsIgnoreFile

require 'functions.php';

$file = 'reports/phpunit.txt';
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
