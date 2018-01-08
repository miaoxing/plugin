<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\DefaultScopeTrait;

class TestDefaultScope extends BaseModel
{
    use DefaultScopeTrait;

    protected $tableV2 = true;

    protected $initV2 = true;

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
