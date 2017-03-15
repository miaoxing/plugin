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

    public function getTags()
    {
        return $this->belongsToMany('testTag', 'test_article_test_tag', 'test_tag_id', 'test_article_id');
    }
}
