<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\Model;

/**
 * @property int|null $id
 * @property string|null $name
 * @property int|null $userCount
 */
class TestGetSet extends Model
{
    protected $casts = [
        'id' => 'int',
        'name' => 'string',
        'user_count' => 'int',
    ];
}
