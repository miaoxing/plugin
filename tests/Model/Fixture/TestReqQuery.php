<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model\Fixture;

use Miaoxing\Plugin\Model\ReqQueryTrait;
use Wei\BaseModel;
use Wei\ModelTrait;

class TestReqQuery extends BaseModel
{
    use ModelTrait;
    use ReqQueryTrait;

    /**
     * @Relation
     */
    public function detail()
    {
        return $this->hasOne(TestReqQueryDetail::class);
    }

    public function customDetail()
    {
        return $this->hasOne(TestReqQueryDetail::class);
    }
}
