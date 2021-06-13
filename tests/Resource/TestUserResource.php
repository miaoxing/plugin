<?php

namespace MiaoxingTest\Plugin\Resource;

use Miaoxing\Plugin\Resource\BaseResource;
use Miaoxing\Plugin\Service\WeiBaseModel;

class TestUserResource extends BaseResource
{
    /**
     * @phpcs:disable Generic.CodeAnalysis.UselessOverridingMethod.Found
     */
    public function toArray(WeiBaseModel $model): array
    {
        return parent::toArray($model);
    }
}
