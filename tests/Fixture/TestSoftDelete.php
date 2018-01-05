<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

/**
 * @property string id
 * @property string deleted_at
 */
class TestSoftDelete extends BaseModel
{
    use SoftDeleteTrait;

    protected $deletedAtColumn = 'deleted_at';

    protected $deletedByColumn = 'deleted_by';

    protected $enableProperty = true;

    protected $tableV2 = true;

    protected $initV2 = true;
}
