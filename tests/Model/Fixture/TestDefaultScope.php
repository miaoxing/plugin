<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\WeiModel;

class TestDefaultScope extends WeiModel
{
    public function __construct(array $options = [])
    {
        parent::__construct($options);

        $this->addDefaultScope('active');
        $this->addDefaultScope('typeA');
    }

    public function active()
    {
        return $this->where('active', true);
    }

    public function typeA()
    {
        return $this->where('type', 'A');
    }
}
