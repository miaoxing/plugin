<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property string|null $id
 * @property string|null $deleted_at
 * @property string|null $purged_at
 */
class TestSoftDeleteTrash extends WeiBaseModel
{
    use ModelTrait;
    use SoftDeleteTrait;

    protected $enableTrash = true;
}
