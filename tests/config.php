<?php

return [
    /* @see Wei\Wei */
    'wei' => [
        'aliases' => [
            'plugin' => Miaoxing\Plugin\Service\Plugin::class,
        ],
        'preload' => [
            'error',
            'env',
            'plugin',
        ],
    ],

    /* @see Wei\Env */
    'env' => [
        'configFile' => 'storage/configs/%env%.php',
    ],
];
