<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Plugin\Service\WeiBaseModel;

class TestReqQuery extends WeiBaseModel
{
    use ModelTrait;
    use ReqQueryTrait;

    public function detail()
    {
        return $this->hasOne(TestReqQueryDetail::class);
    }
}
