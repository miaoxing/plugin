<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

class TestDefaultScope extends WeiBaseModel
{
    use ModelTrait {
        __construct as parentConstruct;
    }

    public function __construct(array $options = [])
    {
        $this->parentConstruct($options);

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
