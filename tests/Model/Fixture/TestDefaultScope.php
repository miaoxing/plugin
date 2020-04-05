<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\BaseModelV2;
use Miaoxing\Plugin\Model\DefaultScopeTrait;

class TestDefaultScope extends BaseModelV2
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->addDefaultScope('active');
        $this->addDefaultScope('typeA');
    }

    public function active()
    {
        return $this->andWhere(['active' => true]);
    }

    public function typeA()
    {
        return $this->andWhere(['type' => 'A']);
    }
}
