<?php

use Miaoxing\Plugin\Service\Model;

return [
    // 服务容器
    'wei' => [
        'debug' => true,
        'inis' => [
            'display_errors' => true,
            'error_reporting' => E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED,
            'date.timezone' => 'Asia/Shanghai',
        ],
        'aliases' => [
            'plugin' => \Miaoxing\Plugin\Service\Plugin::class,
            'request' => \Miaoxing\Services\Service\Request::class,
        ],
        'providers' => [
            'cache' => 'arrayCache',
        ],
        'preload' => [
            'error',
            'plugin',
        ],
    ],
    'app' => [
        'namespace' => 'app',
    ],
    'db' => [
        'host' => 'mysql',
        'port' => 3306,
        'user' => 'root',
        'dbname' => 'miaoxing',
        'charset' => 'utf8mb4',
        'password' => getenv('MYSQL_PASSWORD') ?: 'password',
        'recordClass' => Model::class,
    ],
    'router' => [
        'namespaces' => ['admin', 'api', 'cli'],
    ],
    // 视图
    'view' => [
        'defaultLayout' => '@plugin/layouts/default.php',
    ],
    // 语言翻译
    't' => [
        'locale' => 'zh-CN',
    ],
    'phpFileCache' => [
        'dir' => 'data/cache',
    ],
    'nearCache' => [
        'providers' => [
            'front' => 'arrayCache',
            'back' => 'cache',
        ],
    ],
    'error.logger' => [

    ],
    'mail' => [
        'options' => [
            'Mailer' => 'smtp',
        ],
    ],
    'schema' => [
        'charset' => 'utf8mb4',
        'collate' => 'utf8mb4_unicode_ci',
    ],
    'config' => [
        // TODO 测试时不用写入配置
        'version' => '2200-01-01',
    ],
];
