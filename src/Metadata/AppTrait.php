<?php

namespace Miaoxing\Plugin\Metadata;

use Miaoxing\Plugin\Model\ModelTrait;

/**
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
    use ModelTrait;
}
