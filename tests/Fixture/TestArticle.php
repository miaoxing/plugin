<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

class TestArticle extends BaseModel
{
    protected $table = 'test_articles';

    public function getUser()
    {
        return $this->belongsTo('testUser');
    }
}
