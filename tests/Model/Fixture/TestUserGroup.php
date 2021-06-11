<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property TestUser|TestUser[] $users
 */
class TestUserGroup extends WeiBaseModel
{
    use ModelTrait;

    public function users()
    {
        return $this->hasMany(TestUser::class, 'group_id');
    }
}
