<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\CamelCaseTrait;

/**
 * @property mixed $virtualColumn
 * @property string $firstName
 * @property string $lastName
 * @property string $fullName
 */
class TestVirtualCamelCase extends TestVirtual
{
    use CamelCaseTrait;

    protected $virtual = [
        'virtualColumn',
        'fullName',
    ];

    public function getFullNameAttribute()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function setFullNameAttribute($fullName)
    {
        [$this->firstName, $this->lastName] = explode(' ', $fullName);
    }
}
