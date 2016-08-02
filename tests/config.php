<?php
return [
    // 服务容器
    'wei' => [
        'debug' => true,
        'inis' => [
            'display_errors' => true,
            'error_reporting' => E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED,
            'date.timezone' => 'Asia/Shanghai'
        ],
        'aliases' => [
            'plugin' => 'miaoxing\plugin\services\Plugin'
        ],
        'providers' => [
            'cache' => 'memcache',
        ],
        'preload' => [
            'error',
            'plugin'
        ]
    ],
    'app' => [
        'namespace' => 'test'
    ],
    // 各项目的数据库
    'db' => [
        'host' => 'mysql',
        'port' => 3306,
        'user' => 'root',
        'dbname' => 'test',
        'charset' => 'utf8mb4',
        'password' => getenv('MYSQL_PASSWORD'),
        'recordClass' => 'miaoxing\plugin\BaseModel',
    ],
    // 产品核心的数据库
    'app.db' => [
        'host' => 'mysql',
        'port' => 3306,
        'user' => 'root',
        'dbname' => 'app',
        'charset' => 'utf8mb4',
        'password' => getenv('MYSQL_PASSWORD'),
        'recordClass' => 'miaoxing\plugin\BaseModel',
    ],
    // 视图
    'view' => [
        'defaultLayout' => 'plugin:layouts/default.php',
    ],
    // 语言翻译
    't' => [
        'locale' => 'zh-CN'
    ],
    'phpFileCache' => [
        'dir' => 'data/cache'
    ],
    'nearCache' => [
        'providers' => [
            'front' => 'arrayCache',
            'back' => 'cache',
        ]
    ],
    'js.raven' => [
    ],
    'plugin' => [
        'curNamespace' => 'miaoxing\plugin'
    ]
];