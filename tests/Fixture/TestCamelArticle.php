<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Traits\CamelCase;

class TestCamelArticle extends BaseModel
{
    use CamelCase;

    protected $table = 'test_articles';
}
