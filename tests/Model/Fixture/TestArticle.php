<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property TestUser|null $user
 * @property TestUser|null $editor
 * @property TestTag|TestTag[] $tags
 * @property int $test_user_id
 * @property int $editor_id
 * @property string $title
 */
class TestArticle extends WeiBaseModel
{
    use ModelTrait;

    public function user()
    {
        return $this->belongsTo(TestUser::class);
    }

    public function editor()
    {
        return $this->belongsTo(TestUser::class, 'id', 'editor_id');
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
