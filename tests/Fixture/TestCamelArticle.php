<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Traits\CamelCase;
use Miaoxing\Plugin\Traits\HasCast;

class TestCamelArticle extends BaseModel
{
    use CamelCase;
    use HasCast;

    protected $table = 'test_articles';

    protected $toArrayV2 = true;

    protected $casts = [
        'id' => 'int',
        'test_user_id' => 'int',
        'title' => 'string',
        'content' => 'string',
    ];
}
