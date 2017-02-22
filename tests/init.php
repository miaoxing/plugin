<?php

use Composer\Autoload\ClassLoader;

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
