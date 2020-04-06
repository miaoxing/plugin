<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Service\Model;

class TestReqQuery extends Model
{
    public function detail()
    {
        return $this->hasOne(TestReqQueryDetail::class);
    }
}
