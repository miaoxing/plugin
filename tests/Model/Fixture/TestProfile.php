<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property string|null $test_user_id
 * @property string|null $description
 */
class TestProfile extends WeiBaseModel
{
    use ModelTrait;
}
