<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

/**
 * @property int|null $id
 * @property string|null $setter
 * @property string $getter
 * @property string $mutator
 * @property string $default_value
 * @property \stdClass $object
 */
class TestMutator extends WeiBaseModel
{
    use ModelTrait;

    protected function getGetterAttribute()
    {
        return base64_decode($this->attributes['getter'] ?? '', true);
    }

    protected function setSetterAttribute($value)
    {
        $this->attributes['setter'] = base64_encode($value);
    }

    protected function getMutatorAttribute()
    {
        return base64_decode($this->attributes['mutator'] ?? '', true);
    }

    protected function setMutatorAttribute($value)
    {
        $this->attributes['mutator'] = base64_encode($value);
    }

    protected function getDefaultValueAttribute()
    {
        return $this->attributes['default_value'] ?? 'default value';
    }

    protected function setObjectAttribute($value)
    {
        $this->attributes['object'] = json_encode($value);
    }

    protected function getObjectAttribute()
    {
        return json_decode($this->attributes['object']);
    }
}
