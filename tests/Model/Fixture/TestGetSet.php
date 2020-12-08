<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property int|null $id
 * @property string|null $name
 * @property int|null $user_count
 */
class TestGetSet extends WeiBaseModel
{
    use ModelTrait;

    protected $casts = [
        'id' => 'int',
        'name' => 'string',
        'user_count' => 'int',
    ];
}
