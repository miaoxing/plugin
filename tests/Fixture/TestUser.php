<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

/**
 * @property TestProfile $profile
 * @property TestArticle|TestArticle[] $articles
 */
class TestUser extends BaseModel
{
    protected $table = 'test_users';

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
