<?php

return PhpCsFixer\Config::create()
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('vendor')
            ->in(getcwd())
    )
    ->setRules([
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        'array_syntax' => ['syntax' => 'short'],
        'single_line_comment_style' => [
            // Allow /* @see xxx */
            'comment_types' => ['hash'],
        ],
        'concat_space' => [
            'spacing' => 'one',
        ],
    ]);