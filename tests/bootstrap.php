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

$wei = wei($config);

// 初始化数据库
$db = $wei->db;
$db->executeUpdate('CREATE DATABASE IF NOT EXISTS app;');
$db->useDb('app');

// 动态加载所有插件的dev类,用于主项目中测试子插件
foreach ($wei->plugin->getAll() as $plugin) {
    $file = $plugin->getBasePath() . '/composer.json';
    if (!is_file($file)) {
        continue;
    }
    $composer = json_decode(file_get_contents($file), true);
    foreach ($composer['autoload-dev'] as $autoload) {
        foreach ($autoload as $prefix => $path) {
            $loader->addPsr4($prefix, $plugin->getBasePath() . '/' . $path);
        }
    }
}

if (isset($config['test']['skipSql']) && $config['test']['skipSql']) {
    return;
}

// TODO 待更新为migration模式?
// 1. 获取各插件的SQL文件
$sqlFiles = [];
foreach ($wei->plugin->getAll() as $plugin) {
    $basePath = $plugin->getBasePath();
    $sqlFiles = array_merge($sqlFiles, glob(($basePath ?: '.') . '/docs/*.sql'));
}

// 2. 逐个运行
// 临时指定所在数据库
$appTables = [
    'apps',
];
foreach ($sqlFiles as $file) {
    $table = basename($file, '.sql');
    $db = in_array($table, $appTables) ? $wei->appDb : $wei->db;
    $result = $db->fetch('SHOW TABLES LIKE ?', $table);
    if (!$result) {
        $db->executeUpdate(file_get_contents($file));
    }
}

// 3. 逐个安装插件
foreach ($wei->plugin->getAll() as $plugin) {
    $wei->plugin->install($plugin->getId());
}

// 4. 执行迁移语句
$wei->migration->migrate();
