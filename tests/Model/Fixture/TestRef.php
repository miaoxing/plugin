<?php

declare(strict_types=1);

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

    protected $columns = [
        'id' => [
            'cast' => 'int',
        ],
        'json' => [
            'cast' => 'json',
            'default' => [],
        ],
        'mixed' => [
            'cast' => null,
        ],
    ];
}
