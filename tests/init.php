<?php

use Composer\Autoload\ClassLoader;
use Miaoxing\Plugin\Service\AppModel;
use Miaoxing\Plugin\Service\UserModel;
use Wei\Password;

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
$isCi = false;
foreach (['TRAVIS', 'WERCKER'] as $ci) {
    if (getenv($ci)) {
        $isCi = true;
        $files[] = 'config-' . strtolower($ci) . '.php';
    }
}

$isCi = true;
$config = [];
foreach ($files as $file) {
    if (stream_resolve_include_path($file)) {
        $config = array_replace_recursive($config, require $file);
    }
}

$wei = wei($config);

// NOTE: 安装需依赖CI环境的配置，暂时放到这里
if ($isCi) {
    $out = static function ($message) {
        fwrite(STDOUT, $message . "\n");
    };

    // 1. 初始化数据库
    $db = $wei->db;
    $db->executeUpdate('CREATE DATABASE IF NOT EXISTS ' . $db->getDbname());
    $db->useDb($db->getDbname());

    // 2. 执行迁移语句
    $wei->migration->migrate();

    // 3. 创建默认应用和用户
    AppModel::findByOrCreate([
        'userId' => 1,
        'name' => 'app',
    ]);
    UserModel::findByOrCreate([
        'username' => 'admin',
        'password' => Password::hash('password'),
    ]);

    // 4. 逐个安装插件
    foreach ($wei->plugin->getAll() as $plugin) {
        $ret = $wei->plugin->install($plugin->getId());
        $out($plugin->getId() . ': ' . $ret['message']);
    }

    $out('Install successfully');
}
