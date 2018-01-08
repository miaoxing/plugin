<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModelV2;

/**
 * @property string $setter
 * @property string $getter
 * @property string $mutator
 */
class TestMutator extends BaseModelV2
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
        return base64_decode($this->data['getter']);
    }

    protected function setMutatorAttribute($value)
    {
        $this->data['mutator'] = base64_encode($value);
    }
}
