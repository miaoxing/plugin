<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Model\CastTrait;

/**
 * @property int int_column
 * @property bool bool_column
 * @property string string_column
 * @property string datetime_column
 * @property string date_column
 * @property array json_column
 */
class TestCast extends BaseModel
{
    use CastTrait;

    protected $table = 'test_casts';

    protected $primaryKey = 'int_column';

    protected $enableProperty = true;

    protected $casts = [
        'int_column' => 'int',
        'bool_column' => 'bool',
        'string_column' => 'string',
        'datetime_column' => 'datetime',
        'date_column' => 'date',
        'json_column' => 'json',
    ];

    protected $data = [
        'json_column' => []
    ];
}
