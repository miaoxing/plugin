<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\Model;

/**
 * @property TestArticle|TestArticle[] $articles
 * @property string $name
 */
class TestTag extends Model
{
    public function articles()
    {
        return $this->belongsToMany(TestArticle::class);
    }
}
