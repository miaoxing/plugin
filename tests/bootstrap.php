<?php

require 'init.php';

$loader = require 'vendor/autoload.php';
$wei = wei();

$app = isset($_SERVER['WEI_APP']) ? $_SERVER['WEI_APP'] : 'app';

$wei->app->setNamespace($app);
$wei->event->trigger('appInit');

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

// 允许运行 wei/wei 的测试
$paths = [
    'packages/wei',
    'vendor/wei/wei',
];
foreach ($paths as  $path) {
    if (is_dir($path)) {
        $loader->addPsr4('WeiTest\\', $path . '/tests/unit');
        break;
    }
}
