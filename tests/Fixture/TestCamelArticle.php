<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

class TestCamelArticle extends BaseModel
{
    protected $table = 'test_articles';

    protected $camel = true;
}
