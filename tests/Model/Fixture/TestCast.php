<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\WeiModel;

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
class TestCast extends WeiModel
{
    protected $primaryKey = 'int_column';

    protected $casts = [
        'int_column' => 'int',
        'bool_column' => 'bool',
        'string_column' => 'string',
        'datetime_column' => 'datetime',
        'date_column' => 'date',
        'json_column' => 'array',
        'list_column' => 'list',
        'list2_column' => [
            'list',
            'type' => 'int',
            'separator' => '|',
        ],
    ];

    protected $data = [
        'json_column' => [],
        'list_column' => [],
        'list2_column' => [],
    ];

    public function changeDataBeforeSave()
    {
        $this->string_column = count($this->json_column);
    }
}
