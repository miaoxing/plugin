<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Model\DefaultScopeTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

class TestDefaultScope extends BaseModel
{
    use DefaultScopeTrait;

    protected $table = 'test_default_scopes';

    public function __construct(array $options = array())
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
