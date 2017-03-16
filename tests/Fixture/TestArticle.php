<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

/**
 * @property TestUser $user
 * @property TestTag|TestTag[] tags
 */
class TestArticle extends BaseModel
{
    protected $table = 'test_articles';

    public function user()
    {
        return $this->belongsTo('testUser');
    }

    public function tags()
    {
        return $this->belongsToMany('testTag');
    }

    public function customTags()
    {
        return $this->belongsToMany('testTag')->andWhere('test_tags.id > ?', 0);
    }

    public static function className()
    {
        return get_called_class();
    }
}
