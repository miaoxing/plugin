<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\WeiModel;

/**
 * @property int|null $id
 * @property string|null $setter
 * @property string $getter
 * @property string $mutator
 * @property string $default_value
 */
class TestMutator extends WeiModel
{
    protected function getGetterAttribute()
    {
        return base64_decode($this->data['getter'] ?? '', true);
    }

    protected function setSetterAttribute($value)
    {
        $this->data['setter'] = base64_encode($value);
    }

    protected function getMutatorAttribute()
    {
        return base64_decode($this->data['mutator'] ?? '', true);
    }

    protected function setMutatorAttribute($value)
    {
        $this->data['mutator'] = base64_encode($value);
    }

    protected function getDefaultValueAttribute()
    {
        return $this->data['default_value'] ?? 'default value';
    }
}
