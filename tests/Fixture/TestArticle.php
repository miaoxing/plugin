<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModel;

/**
 * @property TestUser $user
 * @property TestTag|TestTag[] $tags
 * @property string $test_user_id
 * @property string $title
 */
class TestArticle extends BaseModel
{
    protected $toArrayV2 = true;

    protected $enableProperty = true;

    protected $tableV2 = true;

    protected $initV2 = true;

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
}
