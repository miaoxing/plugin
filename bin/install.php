<?php

require 'functions.php';

// 1. 加载配置,初始化服务容器
$dirs = [
    '.',
    'plugins/plugin',
];
foreach ($dirs as $dir) {
    if (is_file($dir . '/tests/init.php')) {
        require $dir . '/tests/init.php';
    }
}
$wei = wei();

// 2. 初始化数据库
$db = $wei->db;
$db->executeUpdate('CREATE DATABASE IF NOT EXISTS app;');
$db->useDb('app');

// TODO 待更新为migration模式?
// 3. 获取各插件的SQL文件
$sqlFiles = [];
foreach ($wei->plugin->getAll() as $plugin) {
    $basePath = $plugin->getBasePath();
    $sqlFiles = array_merge($sqlFiles, glob(($basePath ?: '.') . '/docs/*.sql'));
}

// 4. 逐个运行
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

// 5. 逐个安装插件
foreach ($wei->plugin->getAll() as $plugin) {
    $wei->plugin->install($plugin->getId());
}

// 6. 执行迁移语句
$wei->migration->migrate();

// 7. 尝试移除缓存
$wei->cache->remove('tableFieldsapp.apps');
