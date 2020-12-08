<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property int $id
 * @property array $json
 * @property mixed $mixed
 */
class TestRef extends WeiBaseModel
{
    use ModelTrait;

    protected $casts = [
        'id' => 'int',
        'json' => 'json',
    ];

    protected $data = [
        'json' => [],
    ];
}
