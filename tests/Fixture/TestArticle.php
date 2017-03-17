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

    /**
     * NOTE: 使用参数是避免和父类方法冲突
     *
     * @link https://travis-ci.org/miaoxing/plugin/jobs/211982291
     */
    public function tags($tags = null)
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
