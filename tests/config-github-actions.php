<?php

return [
    'wei' => [
        'providers' => [
            'cache' => 'phpFileCache',
        ],
    ],
    'db' => [
        'host' => '127.0.0.1',
        'port' => getenv('DB_PORT'),
    ],
    'redis' => [
        'host' => '127.0.0.1',
        'port' => getenv('REDIS_PORT')
    ],
];
