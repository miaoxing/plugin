<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Traits\HasCast;

class TestHasCast extends BaseModel
{
    use HasCast;

    protected $table = 'test_has_casts';

    protected $primaryKey = 'int_column';

    protected $casts = [
        'int_column' => 'int',
        'bool_column' => 'bool',
        'string_column' => 'string',
        'datetime_column' => 'datetime',
        'date_column' => 'date',
        'json_column' => 'json',
    ];
}
