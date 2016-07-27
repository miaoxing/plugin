<?php

require 'vendor/autoload.php';

$files = [
    'config.php',
    'tests/config.php'
];

$config = [];
foreach ($files as $file) {
    if (stream_resolve_include_path($file)) {
        $config = array_replace_recursive($config, require $file);
    }
}

$wei = wei($config);