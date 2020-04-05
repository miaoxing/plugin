<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\Model;

/**
 * @property TestProfile $profile
 * @property TestArticle|TestArticle[] $articles
 * @property TestArticle|TestArticle[] $customArticles
 * @property string $id
 */
class TestUser extends Model
{
    public function articles()
    {
        return $this->hasMany(TestArticle::class);
    }

    public function customArticles()
    {
        return $this->hasMany(TestArticle::class)
            ->where('title', 'LIKE', 'Article%')
            ->desc('id');
    }

    public function profile()
    {
        return $this->hasOne(TestProfile::new());
    }
}
