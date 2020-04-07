<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Model;
use Miaoxing\Plugin\Service\QueryBuilder;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestUser;

class StaticCallTest extends BaseTestCase
{
    public function testCallSelf()
    {
        // 1. 有提示，也能正确跳转
        $qb = QueryBuilder::asc('id')->asc('id');
        $this->assertInstanceOf(QueryBuilder::class, $qb);
    }

    public function testCallParent()
    {
        // 1. 有提示，只能跳转自己或父类的 auto-completion 里，不能调到类定义
        $qb = Model::asc('id')->asc('id');
        $this->assertInstanceOf(Model::class, $qb);
    }

    public function testCallParentWithoutAutoCompletionDoc()
    {
        // 1. 第一个有提示或第二个有提示，只能跳转到父类的 auto-completion 里
        $qb = TestUser::asc('id')->asc('id');
        $this->assertInstanceOf(TestUser::class, $qb);
    }
}
