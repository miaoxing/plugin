<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModelV2;

/**
 * @property TestProfile $profile
 * @property TestArticle|TestArticle[] $articles
 * @property TestArticle|TestArticle[] $customArticles
 * @property string $id
 */
class TestUser extends BaseModelV2
{
    public function articles()
    {
        return $this->hasMany(wei()->testArticle());
    }

    public function customArticles()
    {
        return $this->hasMany('testArticle')
            ->andWhere('title LIKE ?', 'Article%')
            ->desc('id');
    }

    public function profile()
    {
        return $this->hasOne('testProfile');
    }
}
