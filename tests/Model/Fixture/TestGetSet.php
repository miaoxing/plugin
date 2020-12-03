<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\WeiModel;

/**
 * @property int|null $id
 * @property string|null $name
 * @property int|null $user_count
 */
class TestGetSet extends WeiModel
{
    protected $casts = [
        'id' => 'int',
        'name' => 'string',
        'user_count' => 'int',
    ];
}
