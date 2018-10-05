<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModelV2;

/**
 * @property int $id
 * @property string $name
 */
class TestGetSet extends BaseModelV2
{
    protected $casts = [
        'id' => 'int',
        'name' => 'string',
    ];
}
