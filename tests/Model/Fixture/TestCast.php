<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\Model;

/**
 * @property int $intColumn
 * @property bool $boolColumn
 * @property string $stringColumn
 * @property string|null $datetimeColumn
 * @property string|null $dateColumn
 * @property array $jsonColumn
 * @property array $listColumn
 * @property array $list2Column
 */
class TestCast extends Model
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
        $this->stringColumn = count($this->jsonColumn);
    }
}
