<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\Model;

/**
 * @property TestUser $user
 * @property TestTag|TestTag[] $tags
 * @property string $testUserId
 * @property string $title
 */
class TestArticle extends Model
{
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
