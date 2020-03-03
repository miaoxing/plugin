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

// 3. 执行迁移语句
$wei->migration->migrate();

// 4. 逐个安装插件
foreach ($wei->plugin->getAll() as $plugin) {
    $wei->plugin->install($plugin->getId());
}
