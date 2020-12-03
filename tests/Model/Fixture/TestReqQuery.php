<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Plugin\Service\WeiModel;

class TestReqQuery extends WeiModel
{
    use ReqQueryTrait;

    public function detail()
    {
        return $this->hasOne(TestReqQueryDetail::class);
    }
}
