<?php

use Composer\Autoload\ClassLoader;
use Miaoxing\Plugin\Service\Jwt;
use Miaoxing\Plugin\Service\UserModel;
use Wei\Password;

require 'functions.php';

// NOTE：解决 PHPStorm 2019.2 的 PHPUnit 在测试目录下运行导致加载不到类错误
$dir = getcwd();
while ('/' !== $dir) {
    if (is_file($dir . '/vendor/autoload.php')) {
        chdir($dir);
        break;
    }
    $dir = dirname($dir);
}

/** @var ClassLoader $loader */
$loader = require 'vendor/autoload.php';

$files = [
    'config/config.php', // 用于 plugin 自己
    'plugins/plugin/config/config.php', // 用于其他插件
    'tests/config.php',
    'tests/config-local.php',
];
$wei = wei(getConfig($files));

// Add configuration file for CI
$isCi = false;
$files = [];
foreach (['GITHUB_ACTIONS'] as $ci) {
    if (getenv($ci)) {
        $isCi = true;
        $files[] = 'config-' . strtolower(strtr($ci, '_', '-')) . '.php';
    }
}
$wei->setConfig(getConfig($files));

// 测试前生成 jwt key
if (!is_file(Jwt::getPublicKey())) {
    Jwt::generateDefaultKeys();
}

// NOTE: 安装需依赖CI环境的配置，暂时放到这里
if ($isCi) {
    $out = static function ($message) {
        fwrite(\STDOUT, $message . "\n");
    };

    // 1. 初始化数据库
    $db = $wei->db;
    $db->executeUpdate('CREATE DATABASE IF NOT EXISTS ' . $db->getDbname());
    $db->useDb($db->getDbname());

    // 2. 执行迁移语句
    $wei->migration->migrate();

    // 3. 创建默认应用和用户
    // 直接创建，如果数据表没有记录，调用 AppModel 会加载事件，从数据表查找当前应用，导致出错
    $wei->db->insert('apps', ['user_id' => 1, 'name' => 'app']);
    UserModel::findByOrCreate([
        'id' => '1',
        'username' => 'admin',
        'password' => Password::hash('password'),
        'adminType' => UserModel::ADMIN_TYPE_SUPER,
    ]);

    // 4. 逐个安装插件
    foreach ($wei->plugin->getAll() as $plugin) {
        $ret = $wei->plugin->install($plugin->getId());
        $out($plugin->getId() . ': ' . $ret->getMessage());
    }

    $out('Install successfully');
}
