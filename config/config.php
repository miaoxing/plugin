<?php

return [
    /* @see Wei\Wei */
    'wei' => [
        'debug' => true,
        'inis' => [
            'display_errors' => true,
            'error_reporting' => E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED,
            'date.timezone' => 'Asia/Shanghai',
        ],
        'aliases' => [
            'request' => Miaoxing\Services\Service\Request::class,
        ],
        'providers' => [
            'cache' => 'phpFileCache',
        ],
    ],

    /* @see Wei\Asset */
    'asset' => [
        'version' => false,
        'baseUrl' => 'http://localhost:8080/dist/',
    ],

    /* @see Wei\Db */
    'db' => [
        'host' => 'mysql',
        'dbname' => 'miaoxing',
        'user' => 'root',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'recordClass' => Miaoxing\Plugin\Service\Model::class,
        'beforeQuery' => [Miaoxing\Services\Callback\Db::class, 'beforeQuery'],
        'providers' => [
            'cache' => 'nearCache',
        ],
    ],

    /* @see Wei\Error */
    'error' => [
        'view' => __DIR__ . '/../views/errors/error.php',
        'message' => '很抱歉，系统繁忙，请稍后再试。',
        'message404' => '很抱歉，您访问的页面不存在，请检查后再试。',
    ],

    /* @see Wei\Http */
    'http' => [
        'timeout' => 5000,
        'retries' => 2,
        'success' => [Miaoxing\Services\Callback\Http::class, 'success'],
        'error' => [Miaoxing\Services\Callback\Http::class, 'error'],
    ],

    /* @see Wei\NearCache */
    'nearCache' => [
        'providers' => [
            'front' => 'arrayCache',
            'back' => 'cache',
        ],
    ],

    /* @see Wei\PhpFileCache */
    'phpFileCache' => [
        'dir' => 'data/cache',
    ],

    /* @see Wei\Redis */
    'redis' => [
        'host' => 'redis',
        'auth' => 'password',
    ],

    /* @see Wei\Ret */
    'ret' => [
        'defaults' => [
            'message' => '操作成功',
            'code' => 1,
        ],
    ],

    /* @see Wei\Router */
    'router' => [
        'namespaces' => ['api', 'admin-api', 'admin'],
        'scopes' => [],
    ],

    /* @see Wei\Schema */
    'schema' => [
        'charset' => 'utf8mb4',
        'collate' => 'utf8mb4_unicode_ci',
    ],

    /* @see Wei\Session */
    'session' => [
        // 尽量延长会话时间,避免用户在微信浏览器中失去登录态
        'inis' => [
            'gc_divisor' => 1000,
            'gc_maxlifetime' => 864000,
            'cookie_httponly' => true,
        ],
    ],

    /* @see Wei\T */
    't' => [
        'locale' => 'zh-CN',
    ],

    /* @see Wei\Upload */
    'upload' => [
        'dir' => 'public/uploads',
    ],

    /* @see Wei\View */
    'view' => [
        'defaultLayout' => '@plugin/layouts/default.php',
    ],
];
