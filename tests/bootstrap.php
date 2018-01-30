<?php

require 'init.php';

$wei = wei();

// 初始化数据库
$db = $wei->db;
$db->useDb('app');

// 动态加载所有插件的dev类,用于主项目中测试子插件
foreach ($wei->plugin->getAll() as $plugin) {
    $file = $plugin->getBasePath() . '/composer.json';
    if (!is_file($file)) {
        continue;
    }
    $composer = json_decode(file_get_contents($file), true);
    if (!isset($composer['autoload-dev'])) {
        continue;
    }
    foreach ($composer['autoload-dev'] as $autoload) {
        foreach ($autoload as $prefix => $path) {
            $loader->addPsr4($prefix, $plugin->getBasePath() . '/' . $path);
        }
    }
}
