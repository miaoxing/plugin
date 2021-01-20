<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\Attributes\Relation;
use Miaoxing\Plugin\Model\CacheTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property TestUserGroup|null $group
 * @property TestProfile|null $profile
 * @property TestArticle|TestArticle[] $articles
 * @property TestArticle|TestArticle[] $customArticles
 * @property int|null $id
 * @property string|null $name
 * @property int|null $group_id
 * @property string|null $address
 */
class TestUser extends WeiBaseModel
{
    use ModelTrait;
    use CacheTrait;

    protected $scopes;

    protected $casts = [
        'id' => 'int',
        'group_id' => 'int',
    ];

    protected $attributes = [
        'group_id' => 0,
    ];

    /**
     * @Relation
     */
    #[Relation]
    public function group()
    {
        return $this->belongsTo(TestUserGroup::class, 'id', 'group_id');
    }

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
        return $this->hasOne(TestProfile::class);
    }

    public function methodHasArg($name)
    {
        return $name;
    }

    public function getAddressAttribute()
    {
        return $this->attributes['address'] ?? 'default address';
    }
}
