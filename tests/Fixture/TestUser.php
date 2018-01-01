<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

/**
 * @property TestProfile $profile
 * @property TestArticle|TestArticle[] $articles
 * @property TestArticle|TestArticle[] $customArticles
 * @property string $id
 */
class TestUser extends BaseModel
{
    protected $table = 'test_users';

    protected $enableProperty = true;

    public function articles()
    {
        return $this->hasMany('testArticle');
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
