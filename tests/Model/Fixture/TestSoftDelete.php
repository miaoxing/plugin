<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\BaseModelV2;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

/**
 * @property string id
 * @property string deleted_at
 */
class TestSoftDelete extends BaseModelV2
{
    use SoftDeleteTrait;
}
