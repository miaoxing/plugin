<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property int $int_column
 * @property bool $bool_column
 * @property string $string_column
 * @property string|null $datetime_column
 * @property string|null $date_column
 * @property array $json_column
 * @property array $list_column
 * @property array $list2_column
 */
class TestCast extends WeiBaseModel
{
    use ModelTrait;

    protected $primaryKey = 'int_column';

    protected $columns = [
        'int_column' => [
            'cast' => 'int',
        ],
        'bool_column' => [
            'cast' => 'bool',
        ],
        'string_column' => [
            'cast' => 'string',
        ],
        'datetime_column' => [
            'cast' => 'datetime',
        ],
        'date_column' => [
            'cast' => 'date',
        ],
        'json_column' => [
            'cast' => 'array',
            'default' => [],
        ],
        'list_column' => [
            'cast' => 'list',
            'default' => [],
        ],
        'list2_column' => [
            'cast' => [
                'list',
                'type' => 'int',
                'separator' => '|',
            ],
            'default' => [],
        ],
    ];
}
