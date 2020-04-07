<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\Model;

/**
 * @property string $setter
 * @property string $getter
 * @property string $mutator
 * @property string $defaultValue
 */
class TestMutator extends Model
{
    protected function getGetterAttribute()
    {
        return base64_decode($this->data['getter']);
    }

    protected function setSetterAttribute($value)
    {
        $this->data['setter'] = base64_encode($value);
    }

    protected function getMutatorAttribute()
    {
        return base64_decode($this->data['mutator']);
    }

    protected function setMutatorAttribute($value)
    {
        $this->data['mutator'] = base64_encode($value);
    }

    protected function getDefaultValueAttribute()
    {
        return $this->data['default_value'] ?: 'default value';
    }
}
