<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Model\CamelCaseTrait;


class Model extends WeiModel
{
    use CamelCaseTrait;

    protected $createdAtColumn = 'createdAt';

    protected $createdByColumn = 'createdBy';

    protected $updatedAtColumn = 'updatedAt';

    protected $updatedByColumn = 'updatedBy';

    protected $deletedAtColumn = 'deletedAt';

    protected $deletedByColumn = 'deletedBy';

    protected $guarded = [
        'id',
        'createdAt',
        'createdBy',
        'updatedAt',
        'updatedBy',
    ];
}
