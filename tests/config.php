<?php

use Miaoxing\Plugin\Service\Model;

return [
    // 服务容器
    'wei' => [
        'debug' => true,
        'inis' => [
            'display_errors' => true,
            'error_reporting' => E_ALL,
            'date.timezone' => 'Asia/Shanghai',
        ],
        'aliases' => [
            'plugin' => \Miaoxing\Plugin\Service\Plugin::class,
            'classMap' => \Miaoxing\Services\Service\ClassMap::class,
        ],
        'providers' => [
            'cache' => 'arrayCache',
        ],
        'preload' => [
            'error',
            'plugin',
        ],
    ],
    'db' => [
        'host' => 'mysql',
        'user' => 'root',
        'dbname' => 'miaoxing',
        'password' => getenv('MYSQL_PASSWORD') ?: 'password',
        'recordClass' => Model::class,
    ],
    'error.logger' => [

    ],
];
