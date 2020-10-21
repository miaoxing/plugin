<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\SoftDeleteTrait;
use Miaoxing\Plugin\Service\Model;

/**
 * @property string|null status
 */
class TestSoftDeleteStatus extends Model
{
    use SoftDeleteTrait;

    const STATUS_NORMAL = 1;

    const STATUS_DELETED = 9;

    protected $table = 'test_soft_deletes';

    protected $deleteStatusColumn = 'status';

    protected $data = [
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
