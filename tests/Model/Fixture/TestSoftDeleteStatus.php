<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property int|null $id
 * @property int|null $status
 */
class TestSoftDeleteStatus extends WeiBaseModel
{
    use ModelTrait;
    use SoftDeleteTrait;

    public const STATUS_NORMAL = 1;

    public const STATUS_DELETED = 9;

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
