<?php

namespace MiaoxingTest\Plugin\Fixture\Job;

use Miaoxing\Plugin\Queue\BaseJob;

/**
 * @mixin \ArrayCachePropMixin
 */
class TestModel extends BaseJob
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;

        parent::__construct();
    }

    public function __invoke(): void
    {
        $this->arrayCache->set('__queue', $this->model->toArray());
    }
}
