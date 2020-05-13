<?php

function getConfig($files)
{
    $config = [];
    foreach ($files as $file) {
        if (stream_resolve_include_path($file)) {
            $config = array_replace_recursive($config, require $file);
        }
    }
    return $config;
}
