<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\AppModel;
use Miaoxing\Plugin\Service\Model;
use Miaoxing\Plugin\Service\QueryBuilder;
use Miaoxing\Plugin\Service\UserModel;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestUser;

/**
 * @internal
 */
final class StaticCallTest extends BaseTestCase
{
    public function testCallSelf()
    {
        // 有提示，也能正确跳转
        $table = QueryBuilder::asc('id')->asc('id')->getTable();

        $this->assertEmpty($table);
    }

    public function testChildLevel1()
    {
        // 序号 参数 是否有代码提示yn 跳转到哪里y:源文件;a:auto文件;s:auto静态类文件,s:auto动态类文件 是否高亮正常y:正常;n：异常；m:有点问题
        // 1. 有提示，只能跳转自己或父类的 auto-completion 里，不能调到类定义
        // 2. excludeParentMethods
        // 3.1 byType,excludeParentMethods 有提示，跳转正常，n
        // 3.2 byType,excludeParentMethods 有提示，跳转到static,y
        // 3.3 byType,excludeParentMethods 有提示，跳转到static,y
        // 4.1 byType,excludeParentMethods,generateEmptyClass yyy,ssa,y
        // 5.1 single,generateEmptyClass yyy,aa(ay)最后一个可以正常跳转,y
        // 5.2 single,generateEmptyClass yyy,yyy,y
        // 5.3 single,generateEmptyClass yyy,yyy,y
        // 6.1 byClass,excludeParentMethods yyy,ddy,m
        // 6.2 byClass,excludeParentMethods yyy,yyy,n
        // 7.1 byClass,generateEmptyClass yyy,aa(a+y),y
        // 7.2 byClass,generateEmptyClass yyy,aa(a+y),y
        // 8.1 single,generateEmptyClass yny,aa(a+y),y
        $table = Model::asc('x')->asc('x')->getTable();
        $this->assertEmpty($table);
    }

    public function testChild2HasDoc()
    {
        // 3.1 byType,excludeParentMethods 有提示，跳转到dynamic，高亮异常
        // 3.2 byType,excludeParentMethods 有提示，跳转到static，高亮正常
        // 3.3 byType,excludeParentMethods 有提示，跳转到static，高亮异常
        // 4.1 byType,excludeParentMethods,generateEmptyClass yyy,dds,y
        // 4.2 byType,excludeParentMethods,generateEmptyClass yyy,dds,m
        // 5.1 single,generateEmptyClass yyy,aay,y // 还有一种最后一个不能正常跳转
        // 5.2 single,generateEmptyClass yyy,aaa,y
        // 5.3 single,generateEmptyClass yyy,aay,y
        // 6.1 byClass,excludeParentMethods yyy,ddd,m
        // 6.2 byClass,excludeParentMethods yyy,yyy,n
        // 7.1 byClass,generateEmptyClass yyy,aa(a+y),y
        // 7.2 byClass,generateEmptyClass yyy,aaa,y
        // 8.1 single,generateEmptyClass ynn,aaa,y
        $table = UserModel::asc('x')->asc('x')->getTable();
        $this->assertSame('users', $table);
    }

    public function testChild2DontHaveApi()
    {
        // 4.1 byType,excludeParentMethods,generateEmptyClass yyy,ssd,y
        // 4.2 byType,excludeParentMethods,generateEmptyClass yyy,dds,m
        // 5.1 single,generateEmptyClass yyy,aaa最后一个也是跳转auto,y
        // 5.2 single,generateEmptyClass yyy,aaa,y
        // 5.3 single,generateEmptyClass yyy,aay,y
        // 6.1 byClass,excludeParentMethods yyy,ddd,m
        // 6.2 byClass,excludeParentMethods yyy,yyd,n
        // 7.1 byClass,generateEmptyClass yyy,aa(a+y),y
        // 7.2 byClass,generateEmptyClass yyy,aaa,y
        // 8.1 single,generateEmptyClass ynn,aaa,y
        $table = AppModel::asc('x')->asc('x')->getTable();
        $this->assertSame('apps', $table);
    }

    public function testChild2DontHaveDoc()
    {
        // 1. 第一个有提示或第二个有提示，只能跳转到父类的 auto-completion 里
        // 3.1 byType,excludeParentMethods yn，跳转正常，n
        // 3.2 byType,excludeParentMethods ny，跳转到static里，y
        // 3.3 byType,excludeParentMethods nn，跳转到static里，n
        // 4.1 byType,excludeParentMethods,generateEmptyClass nyy,dds,y
        // 4.2 byType,excludeParentMethods,generateEmptyClass ynn,dds,m
        // 5.1 single,generateEmptyClass nyy,aaa,y
        // 5.2 single,generateEmptyClass ynn,aaa,y
        // 5.3 single,generateEmptyClass nnn,aay,n
        // 6.1 byClass,excludeParentMethods nyy,ddd,m
        // 6.2 byClass,excludeParentMethods nyy,yyd,n
        // 7.1 byClass,generateEmptyClass nnn,aay,n
        // 7.2 byClass,generateEmptyClass ynn,aaa,y
        // 8.1 single,generateEmptyClass ynn,aaa,y
        $table = TestUser::asc('x')->asc('x')->getTable();
        $this->assertEquals('test_users', $table);
    }
}
