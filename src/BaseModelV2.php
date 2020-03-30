<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Model\CamelCaseTrait;
use Miaoxing\Plugin\Model\CastTrait;
use Miaoxing\Plugin\Model\GetSetTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;

class BaseModelV2 extends BaseModel
{
    use CamelCaseTrait;
    use CastTrait;
    use ReqQueryTrait;

    protected $appIdColumn = 'app_id';

    protected $createdAtColumn = 'created_at';

    protected $createdByColumn = 'created_by';

    protected $updatedAtColumn = 'updated_at';

    protected $updatedByColumn = 'updated_by';

    protected $deletedByColumn = 'deleted_by';

    protected $deletedAtColumn = 'deleted_at';

    protected $userIdColumn = 'user_id';

    protected $toArrayV2 = true;

    protected $tableV2 = true;

    protected $guarded = [
        'id',
        'app_id',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $hidden = [
        'app_id',
        'deleted_at',
        'deleted_by',
    ];
}
