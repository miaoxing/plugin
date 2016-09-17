<?php

return [
    'wei' => [
        'providers' => [
            'cache' => 'fileCache',
        ],
    ],
    'db' => [
        'host' => $_SERVER['MYSQL_PORT_3306_TCP_ADDR'],
        'port' => $_SERVER['MYSQL_PORT_3306_TCP_PORT'],
        'password' => $_SERVER['MYSQL_ENV_MYSQL_ROOT_PASSWORD'],
    ],
    'app.db' => [
        'host' => $_SERVER['MYSQL_PORT_3306_TCP_ADDR'],
        'port' => $_SERVER['MYSQL_PORT_3306_TCP_PORT'],
        'password' => $_SERVER['MYSQL_ENV_MYSQL_ROOT_PASSWORD'],
    ],
    'session' => [
        'inis' => [
            'save_handler' => 'files',
            'save_path' => '',
        ],
    ],
];
