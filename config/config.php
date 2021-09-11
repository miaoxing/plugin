<?php

return [
    /* @see Wei\Wei */
    'wei' => [
        'debug' => true,
        'inis' => [
            'display_errors' => true,
            'error_reporting' => \E_ALL & ~\E_NOTICE & ~\E_STRICT & ~\E_DEPRECATED & ~\E_USER_DEPRECATED,
            'date.timezone' => 'Asia/Shanghai',
        ],
        'aliases' => [
            'request' => Wei\Req::class,
            'plugin' => Miaoxing\Plugin\Service\Plugin::class,
            'pageRouter' => Miaoxing\Plugin\Service\PageRouter::class,
        ],
        'preload' => [
            'error',
            'env',
            'plugin',
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
        'tablePrefix' => 'mx_',
        'beforeQuery' => [Miaoxing\Plugin\Callback\DbCallback::class, 'beforeQuery'],
        'providers' => [
            'cache' => 'nearCache',
        ],
    ],

    /* @see Wei\Env */
    'env' => [
        'configFile' => 'storage/configs/%env%.php',
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
        'success' => [Miaoxing\Plugin\Callback\HttpCallback::class, 'success'],
        'error' => [Miaoxing\Plugin\Callback\HttpCallback::class, 'error'],
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
        'dir' => 'storage/cache',
    ],

    /* @see Wei\Redis */
    'redis' => [
        'host' => 'redis',
        'auth' => 'password',
    ],

    /* @see Wei\Req */
    'req' => [
        // 更改为 false，以便不配置 URL 重写也能打开页面
        'defaultUrlRewrite' => false,
    ],

    /* @see Wei\Ret */
    'ret' => [
        'defaultSucMessage' => '操作成功',
    ],

    /* @see Wei\Schema */
    'schema' => [
        'charset' => 'utf8mb4',
        'collate' => 'utf8mb4_unicode_ci',
        'userIdType' => 'uBigInt',
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

    'url' => [
        'passThroughParams' => [
            'appId',
        ],
    ],

    /* @see Wei\View */
    'view' => [
        'defaultLayout' => '@plugin/layouts/default.php',
    ],
];
