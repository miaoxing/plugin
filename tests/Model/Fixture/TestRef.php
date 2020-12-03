<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\WeiModel;

/**
 * @property int $id
 * @property array $json
 * @property mixed $mixed
 */
class TestRef extends WeiModel
{
    protected $casts = [
        'id' => 'int',
        'json' => 'json',
    ];

    protected $data = [
        'json' => [],
    ];
}
