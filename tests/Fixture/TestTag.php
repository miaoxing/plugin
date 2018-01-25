<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModelV2;

/**
 * @property TestArticle|TestArticle[] $articles
 * @property string $name
 */
class TestTag extends BaseModelV2
{
    public function articles()
    {
        return $this->belongsToMany('testArticle');
    }
}
