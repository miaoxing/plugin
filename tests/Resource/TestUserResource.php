<?php

namespace MiaoxingTest\Plugin\Resource;

use Miaoxing\Plugin\Resource\BaseResource;
use Wei\BaseModel;

class TestUserResource extends BaseResource
{
    /**
     * 用于解决 phpstan 错误
     *
     * tests/Resource/ResourceTest.php
     * 45 Call to protected method toArray() of class Miaoxing\Plugin\Resource\BaseResource.
     * ...
     *
     * @phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
     */
    public function toArray(BaseModel $model): array
    {
        return parent::toArray($model);
    }

    /**
     * @param array|string $includes
     * @return BaseResource
     * @phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
     */
    public function includes($includes): BaseResource
    {
        return parent::includes($includes);
    }
}
