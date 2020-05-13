<?php

use Miaoxing\Plugin\Service\Model;

return [
    /* @see \Wei\Wei 服务容器 */
    'wei' => [
        'debug' => true,
        'inis' => [
            'display_errors' => true,
            'error_reporting' => E_ALL,
        ],
        'aliases' => [
            'plugin' => \Miaoxing\Plugin\Service\Plugin::class,
            'classMap' => \Miaoxing\Services\Service\ClassMap::class,
        ],
        'providers' => [
            'cache' => 'arrayCache',
        ],
        'preload' => [
            'plugin',
        ],
    ],
    /* @see \Wei\Db 数据库 */
    'db' => [
        'host' => 'mysql',
        'user' => 'root',
        'dbname' => 'miaoxing',
        'password' => 'password',
        'recordClass' => Model::class,
    ],
    'error.logger' => [

    ],
    // Optional
    'phpFileCache' => [
        'dir' => 'data/cache',
    ],
    'logger' => [
        'dir' => 'data/logs',
    ],
];
