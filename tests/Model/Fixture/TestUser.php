<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

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

    protected $scopes;

    protected $loadTimes;

    protected $eventResult;

    protected $casts = [
        'id' => 'int',
        'group_id' => 'int',
    ];

    protected $data = [
        'group_id' => 0,
    ];

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

    public function afterLoad()
    {
        ++$this->loadTimes;
    }

    public function getLoadTimes()
    {
        return $this->loadTimes;
    }

    public function beforeCreate()
    {
        $this->eventResult .= 'beforeCreate->';
    }

    public function afterCreate()
    {
        $this->eventResult .= 'afterCreate->';
    }

    public function beforeSave()
    {
        $this->eventResult .= 'beforeSave->';
    }

    public function afterSave()
    {
        $this->eventResult .= 'afterSave';
    }

    public function beforeDestroy()
    {
        $this->eventResult .= 'beforeDestroy->';
    }

    public function afterDestroy()
    {
        $this->eventResult .= 'afterDestroy';
    }

    public function getEventResult()
    {
        return $this->eventResult;
    }

    public function getAddressAttribute()
    {
        return $this->data['address'] ?? 'default address';
    }
}
