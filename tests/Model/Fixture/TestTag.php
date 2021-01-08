<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property TestArticle|TestArticle[] $articles
 * @property string $name
 */
class TestTag extends WeiBaseModel
{
    use ModelTrait;

    public function articles()
    {
        return $this->belongsToMany(TestArticle::class);
    }
}
