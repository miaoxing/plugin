<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\CamelCaseTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

class TestCamelCase extends WeiBaseModel
{
    use ModelTrait;
    use CamelCaseTrait;
}
