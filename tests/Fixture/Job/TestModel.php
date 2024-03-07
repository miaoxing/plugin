<?php

namespace MiaoxingTest\Plugin\Fixture\Job;

use Miaoxing\Plugin\Queue\BaseJob;

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
        $_SERVER['__queue'] = $this->model->toArray();
    }
}
