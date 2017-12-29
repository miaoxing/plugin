<?php

namespace MiaoxingTest\Plugin\Fixture;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Traits\SoftDelete;

class TestSoftDelete extends BaseModel
{
    use SoftDelete;

    protected $table = 'test_soft_deletes';

    protected $deletedAtColumn = 'deleted_at';

    protected $deletedByColumn = 'deleted_by';
}
