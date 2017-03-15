<?php


namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

class TestTag extends BaseModel
{
    protected $table = 'test_tags';

    public function getArticles()
    {
        return $this->belongsToMany('testArticle');
    }

    public static function className()
    {
        return get_called_class();
    }
}
