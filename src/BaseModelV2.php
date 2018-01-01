<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Model\CamelCaseTrait;
use Miaoxing\Plugin\Model\CastTrait;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\QuickQueryTrait;

class BaseModelV2 extends BaseModel
{
    use CamelCaseTrait;
    use CastTrait;
    use HasAppIdTrait;
    use QuickQueryTrait;

    protected $providers = [
        'db' => 'app.db',
    ];

    protected $appIdColumn = 'app_id';

    protected $createdAtColumn = 'created_at';

    protected $updatedAtColumn = 'updated_at';

    protected $createdByColumn = 'created_by';

    protected $updatedByColumn = 'updated_by';

    protected $deletedByColumn = 'deleted_by';

    protected $deletedAtColumn = 'deleted_at';

    protected $userIdColumn = 'user_id';

    protected $toArrayV2 = true;

    protected $enableProperty = true;
}
