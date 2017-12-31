<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

class TestSoftDelete extends BaseModel
{
    use SoftDeleteTrait;

    protected $table = 'test_soft_deletes';

    protected $deletedAtColumn = 'deleted_at';

    protected $deletedByColumn = 'deleted_by';
}
