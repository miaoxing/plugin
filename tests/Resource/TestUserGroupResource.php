<?php

namespace MiaoxingTest\Plugin\Resource;

use Miaoxing\Plugin\Resource\BaseResource;
use MiaoxingTest\Plugin\Model\Fixture\TestUserGroup;

class TestUserGroupResource extends BaseResource
{
    public function transform(TestUserGroup $group): array
    {
        return $group->toArray();
    }
}
