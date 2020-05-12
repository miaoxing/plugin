<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Plugin\Service\Model;

class TestReqQuery extends Model
{
    use ReqQueryTrait;

    public function detail()
    {
        return $this->hasOne(TestReqQueryDetail::class);
    }
}
