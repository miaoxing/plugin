<?php

return Symfony\CS\Config::create()
    ->setUsingCache(true)
    ->fixers([
        'php_unit_construct',
        'short_array_syntax',
        '-concat_without_spaces',
        '-include',
        '-phpdoc_params',
        '-phpdoc_separation',
        '-phpdoc_short_description'
    ])
    ->finder(
        Symfony\CS\Finder::create()
            ->in(__DIR__)
            ->exclude('vendor')
    )
;
