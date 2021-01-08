<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\CamelCaseTrait;
use Miaoxing\Plugin\Model\ModelTrait;

/**
 * @property mixed $virtualColumn
 * @property string $firstName
 * @property string $lastName
 * @property string $fullName
 */
class TestVirtualCamelCase extends TestVirtual
{
    use CamelCaseTrait;

    protected $table = 'test_virtual_camel_cases';

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
