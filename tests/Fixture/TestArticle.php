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
}
