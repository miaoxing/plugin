<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModel;

/**
 * @property TestProfile $profile
 * @property TestArticle|TestArticle[] $articles
 * @property TestArticle|TestArticle[] $customArticles
 * @property string $id
 */
class TestUser extends BaseModel
{
    protected $enableProperty = true;

    protected $tableV2 = true;

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
