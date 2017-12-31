<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Model\CamelCaseTrait;
use Miaoxing\Plugin\Model\CastTrait;

class TestCamelArticle extends BaseModel
{
    use CamelCaseTrait;

    protected $table = 'test_articles';

    protected $toArrayV2 = true;
}
