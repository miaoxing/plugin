<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property TestUser|null $user
 * @property TestTag|TestTag[] $tags
 * @property string $testUserId
 * @property string $title
 */
class TestArticle extends WeiBaseModel
{
    use ModelTrait;

    public function user()
    {
        return $this->belongsTo(TestUser::class);
    }

    /**
     * NOTE: 使用参数是避免和父类方法冲突
     *
     * @param mixed|null $tags
     * @return static
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
