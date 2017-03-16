<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

class TestUser extends BaseModel
{
    protected $table = 'test_users';

    public function getArticles()
    {
        return $this->hasMany('testArticle');
    }

    public function getCustomArticles()
    {
        return $this->hasMany('testArticle')
            ->andWhere("title LIKE ?", 'Article%')
            ->desc('id');
    }

    public function getProfile()
    {
        return $this->hasOne('testProfile');
    }
}
