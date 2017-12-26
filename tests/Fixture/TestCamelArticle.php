<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Traits\CamelCase;

class TestCamelArticle extends BaseModel
{
    use CamelCase;

    protected $table = 'test_articles';

    protected $toArrayV2 = true;

    protected $casts = [
        'id' => 'int',
        'test_user_id' => 'int',
        'title' => 'string',
        'content' => 'string',
    ];
}
