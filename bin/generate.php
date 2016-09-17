<?php

/**
 * 根据提供的插件ID,生成基本的插件目录结构
 */
$id = $argv[1];

ob_start();
require(__DIR__ . '/../templates/plugin.php');
$content = ob_get_clean();

$dir = 'plugins/' . $id;

mkdir($dir);
file_put_contents($dir . '/Plugin.php', $content);

mkdir($dir . '/controllers');
mkdir($dir . '/controllers/admin');
mkdir($dir . '/services');
mkdir($dir . '/views');
mkdir($dir . '/views/admin');

echo "OK\n";
