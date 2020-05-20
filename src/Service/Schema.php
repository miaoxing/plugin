<?php

namespace Miaoxing\Plugin\Service;

class Schema extends \Wei\Schema
{
    /**
     * @var array
     */
    protected $typeDefaults = [
        self::TYPE_BIG_INT => '0',
        self::TYPE_BOOL => '0',
        self::TYPE_CHAR => '',
        self::TYPE_DATE => null,
        self::TYPE_DATETIME => null,
        self::TYPE_DECIMAL => '0',
        self::TYPE_DOUBLE => '0',
        self::TYPE_INT => '0',
        self::TYPE_LONG_TEXT => false,
        self::TYPE_MEDIUM_INT => '0',
        self::TYPE_MEDIUM_TEXT => false,
        self::TYPE_TINY_INT => '0',
        self::TYPE_SMALL_INT => '0',
        self::TYPE_STRING => '',
        self::TYPE_TEXT => false,
        self::TYPE_TIMESTAMP => null,
    ];
}
