<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\BaseModelV2;

/**
 * @property int $id
 * @property string $name
 * @property int $userCount
 */
class TestGetSet extends BaseModelV2
{
    protected $casts = [
        'id' => 'int',
        'name' => 'string',
        'user_count' => 'int',
    ];
}
