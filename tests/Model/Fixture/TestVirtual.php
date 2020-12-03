<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\WeiModel;

/**
 * @property mixed $virtual_column
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 */
class TestVirtual extends WeiModel
{
    protected $table = 'test_virtual';

    protected $virtual = [
        'virtual_column',
        'full_name',
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

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function setFullNameAttribute($fullName)
    {
        [$this->first_name, $this->last_name] = explode(' ', $fullName);
    }
}
