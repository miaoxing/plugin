<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModelV2;

/**
 * @property mixed $virtualColumn
 */
class TestVirtual extends BaseModelV2
{
    protected $table = 'test_virtual';

    protected $virtual = [
        'virtual_column',
    ];

    protected $virtualColumnValue;

    public function getVirtualColumnAttribute()
    {
        return $this->virtualColumnValue;
    }

    public function setVirtualColumnAttribute($value)
    {
        $this->virtualColumnValue = $value;
    }

    public function getVirtualColumnValue()
    {
        return $this->virtualColumnValue;
    }
}
