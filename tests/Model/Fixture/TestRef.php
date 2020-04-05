<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\Model;

/**
 * @property int $id
 * @property array $json
 * @property mixed $mixed
 */
class TestRef extends Model
{
    protected $casts = [
        'id' => 'int',
        'json' => 'json',
    ];

    protected $data = [
        'json' => []
    ];
}
