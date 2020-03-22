<?php

use Composer\Autoload\ClassLoader;

// NOTE：解决 PHPStorm 2019.2 的 PHPUnit 在测试目录下运行导致加载不到类错误
$dir = getcwd();
while ($dir !== '/') {
    if (is_file($dir . '/vendor/autoload.php')) {
        chdir($dir);
        break;
    }
    $dir = dirname($dir);
}

/** @var ClassLoader $loader */
$loader = require 'vendor/autoload.php';

$files = [
    'config.php',
    'tests/config.php',
    'tests/config-local.php',
];

// Add configuration file for CI
foreach (['TRAVIS', 'WERCKER'] as $ci) {
    if (getenv($ci)) {
        $files[] = 'config-' . strtolower($ci) . '.php';
    }
}

$config = [];
foreach ($files as $file) {
    if (stream_resolve_include_path($file)) {
        $config = array_replace_recursive($config, require $file);
    }
}

return wei($config);
