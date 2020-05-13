<?php

return [
    /* @see Wei\Wei */
    'wei' => [
        'aliases' => [
            'plugin' => Miaoxing\Plugin\Service\Plugin::class,
            'classMap' => Miaoxing\Services\Service\ClassMap::class,
        ],
        'preload' => [
            'error',
            'env',
            'plugin',
        ],
    ],

    /* @see Wei\Env */
    'env' => [
        'configFile' => 'data/configs/%env%.php',
    ],
];
