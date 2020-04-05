<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\Model;

/**
 * @property int $intColumn
 * @property bool $boolColumn
 * @property string $stringColumn
 * @property string $datetimeColumn
 * @property string $dateColumn
 * @property array $jsonColumn
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
    ];

    protected $data = [
        'json_column' => [],
    ];
}
