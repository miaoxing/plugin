<?php

namespace MiaoxingTest\Plugin\Fixture\Job;

use Miaoxing\Plugin\Queue\BaseJob;

/**
 * @mixin \ArrayCachePropMixin
 */
class TestJob extends BaseJob
{
    protected $prop1;

    protected $prop2;

    public function __construct($prop1 = null, $prop2 = 2)
    {
        $this->prop1 = $prop1;
        $this->prop2 = $prop2;

        parent::__construct();
    }

    public function __invoke(): void
    {
        $this->arrayCache->set('__prop1', $this->prop1 ?: 'test');
    }

    public function getProp1()
    {
        return $this->prop1;
    }

    /**
     * @return mixed
     */
    public function getProp2()
    {
        return $this->prop2;
    }
}
