<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\Model;

/**
 * @property mixed $virtualColumn
 * @property string $firstName
 * @property string $lastName
 * @property string $fullName
 */
class TestVirtual extends Model
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
        return $this->firstName . ' ' . $this->lastName;
    }

    public function setFullNameAttribute($fullName)
    {
        [$this->firstName, $this->lastName] = explode(' ', $fullName);
    }
}
