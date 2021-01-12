<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property int|null $id
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class TestEvent extends WeiBaseModel
{
    use ModelTrait;

    /**
     * @var array
     */
    protected $eventResult = [];

    protected static $afterFindReturn;

    public static function setAfterFindReturn($return)
    {
        static::$afterFindReturn = $return;
    }

    public function afterFind()
    {
        $this->eventResult[] = __FUNCTION__;
        return static::$afterFindReturn;
    }

    public function beforeCreate()
    {
        $this->eventResult[] = __FUNCTION__;
    }

    public function afterCreate()
    {
        $this->eventResult[] = __FUNCTION__;
    }

    public function beforeUpdate()
    {
        $this->eventResult[] = __FUNCTION__;
    }

    public function afterUpdate()
    {
        $this->eventResult[] = __FUNCTION__;
    }

    public function beforeSave()
    {
        $this->eventResult[] = __FUNCTION__;
    }

    public function afterSave()
    {
        $this->eventResult[] = __FUNCTION__;
    }

    public function beforeDestroy()
    {
        $this->eventResult[] = __FUNCTION__;
    }

    public function afterDestroy()
    {
        $this->eventResult[] = __FUNCTION__;
    }

    public function getEventResult(): string
    {
        return implode('->', $this->eventResult);
    }

    public function addEventResult(string $result)
    {
        $this->eventResult[] = $result;
    }
}
