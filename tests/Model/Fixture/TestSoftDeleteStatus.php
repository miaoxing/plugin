<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property string|null status
 */
class TestSoftDeleteStatus extends WeiBaseModel
{
    use ModelTrait;
    use SoftDeleteTrait;

    const STATUS_NORMAL = 1;

    const STATUS_DELETED = 9;

    protected $table = 'test_soft_deletes';

    protected $deleteStatusColumn = 'status';

    protected $attributes = [
        'status' => self::STATUS_NORMAL,
    ];

    protected function getDeleteStatusValue()
    {
        return static::STATUS_DELETED;
    }

    protected function getRestoreStatusValue()
    {
        return static::STATUS_NORMAL;
    }
}
