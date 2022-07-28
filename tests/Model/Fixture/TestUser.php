<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Wei\BaseModel;
use Wei\Model\CacheTrait;
use Wei\Model\Relation;

/**
 * @property TestUserGroup|null $group
 * @property TestProfile|null $profile
 * @property int|null $id
 * @property string|null $name
 * @property int|null $group_id
 * @property string|null $address
 */
class TestUser extends BaseModel
{
    use CacheTrait;
    use ModelTrait;

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

    public function methodHasArg($name)
    {
        return $name;
    }

    public function getAddressAttribute()
    {
        return $this->attributes['address'] ?? 'default address';
    }
}
