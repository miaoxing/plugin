<?php


namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

/**
 * @property TestArticle|TestArticle[] $articles
 */
class TestTag extends BaseModel
{
    protected $table = 'test_tags';

    public function articles()
    {
        return $this->belongsToMany('testArticle');
    }

    public static function className()
    {
        return get_called_class();
    }
}
