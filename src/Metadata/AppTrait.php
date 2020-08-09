<?php

namespace Miaoxing\Plugin\Metadata;

/**
 * AppTrait
 *
 * @property int $id
 * @property int $userId
 * @property array $pluginIds
 * @property string $name
 * @property string $title
 * @property string $secret
 * @property string $domain
 * @property string $description
 * @property string $industry
 * @property int $status
 * @property array $configs
 * @property string $createdAt
 * @property string $updatedAt
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
        'user_id' => 'int',
        'plugin_ids' => 'array',
        'name' => 'string',
        'title' => 'string',
        'secret' => 'string',
        'domain' => 'string',
        'description' => 'string',
        'industry' => 'string',
        'status' => 'int',
        'configs' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_by' => 'int',
        'updated_by' => 'int',
    ];
}
