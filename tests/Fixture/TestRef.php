<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModelV2;

/**
 * @property int $id
 * @property array $json
 * @property mixed $mixed
 */
class TestRef extends BaseModelV2
{
    protected $casts = [
        'id' => 'int',
        'json' => 'json',
    ];

    protected $data = [
        'json' => []
    ];
}
