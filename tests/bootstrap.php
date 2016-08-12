<?php

require 'vendor/autoload.php';

$files = [
    'config.php',
    'tests/config.php',
    'tests/config-local.php'
];

// Add configuration file for CI
foreach (array('TRAVIS') as $ci) {
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

$wei = wei($config);
