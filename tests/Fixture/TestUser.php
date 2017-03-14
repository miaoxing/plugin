<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;

class TestUser extends BaseModel
{
    protected $table = 'test_users';

    public function getArticles()
    {
        return $this->hasMany('testArticle', 'user_id', 'id');
    }
}
