<?php

namespace Miaoxing\Plugin;

use Wei\BaseModel as WeiBaseModel;
use Wei\Model\CamelCaseTrait;

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
