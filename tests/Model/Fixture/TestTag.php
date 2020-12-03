<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\WeiModel;

/**
 * @property TestArticle|TestArticle[] $articles
 * @property string $name
 */
class TestTag extends WeiModel
{
    public function articles()
    {
        return $this->belongsToMany(TestArticle::class);
    }
}
