<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModel;

/**
 * @property TestArticle|TestArticle[] $articles
 * @property string $name
 */
class TestTag extends BaseModel
{
    protected $enableProperty = true;

    protected $tableV2 = true;

    protected $initV2 = true;

    public function articles()
    {
        return $this->belongsToMany('testArticle');
    }
}
