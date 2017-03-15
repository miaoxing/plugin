<?php


namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

class TestTag extends BaseModel
{
    protected $table = 'test_tags';

    public function getArticles()
    {
        return $this->belongsToMany('testArticle', 'test_article_test_tag', 'test_article_id', 'test_tag_id');
    }
}
