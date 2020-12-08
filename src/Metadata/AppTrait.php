<?php

namespace Miaoxing\Plugin\Metadata;

/**
 * AppTrait
 *
 * @property int $id
 * @property int $userId
 * @property string $pluginIds
 * @property string $name
 * @property string $title
 * @property string $secret
 * @property string $domain
 * @property string $description
 * @property string $industry
 * @property int $status
 * @property string $configs
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * @internal will change in the future
 */
trait AppTrait
{
    /**
     * @var array
     * @see CastTrait::$casts
     */
    protected $casts = [
        'id' => 'int',
        'userId' => 'int',
        'pluginIds' => 'string',
        'name' => 'string',
        'title' => 'string',
        'secret' => 'string',
        'domain' => 'string',
        'description' => 'string',
        'industry' => 'string',
        'status' => 'int',
        'configs' => 'string',
        'createdAt' => 'datetime',
        'updatedAt' => 'datetime',
        'createdBy' => 'int',
        'updatedBy' => 'int',
    ];
}
