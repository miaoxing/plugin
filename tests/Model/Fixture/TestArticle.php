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
        return $this->belongsTo(TestUser::class);
    }

    /**
     * NOTE: 使用参数是避免和父类方法冲突
     *
     * @link https://travis-ci.org/miaoxing/plugin/jobs/211982291
     * @param mixed|null $tags
     */
    public function tags($tags = null)
    {
        return $this->belongsToMany(TestTag::class);
    }

    public function customTags()
    {
        return $this->belongsToMany(TestTag::class)->where('test_tags.id', '>', 0);
    }
}
