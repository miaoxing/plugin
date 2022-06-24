<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Model\CamelCaseTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

abstract class BaseModel extends WeiBaseModel
{
    use CamelCaseTrait;

    protected $createdAtColumn = 'createdAt';

    protected $createdByColumn = 'createdBy';

    protected $updatedAtColumn = 'updatedAt';

    protected $updatedByColumn = 'updatedBy';

    protected $deletedAtColumn = 'deletedAt';

    protected $deletedByColumn = 'deletedBy';

    protected $purgedAtColumn = 'purgedAt';

    protected $purgedByColumn = 'purgedBy';

    protected $guarded = [
        'id',
        'createdAt',
        'createdBy',
        'updatedAt',
        'updatedBy',
    ];

    protected $hidden = [
        'deletedBy',
        'deletedAt',
        'purgedAt',
        'purgedBy',
    ];
}
