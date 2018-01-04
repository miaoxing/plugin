<?php


namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModel;

/**
 * @property TestArticle|TestArticle[] $articles
 * @property string $name
 */
class TestTag extends BaseModel
{
    protected $table = 'test_tags';

    protected $enableProperty = true;

    public function articles()
    {
        return $this->belongsToMany('testArticle');
    }

    public static function className()
    {
        return get_called_class();
    }
}
