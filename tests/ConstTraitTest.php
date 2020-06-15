<?php

namespace MiaoxingTest\Plugin;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\SexConst;

class ConstTraitTest extends BaseTestCase
{
    public function testGetConstants()
    {
        $sex = new SexConst();

        $consts = $sex->getConsts('sex');

        $this->assertCount(2, $consts);
        $this->assertEquals([
            1 => [
                'id' => SexConst::SEX_MALE,
                'key' => 'male',
                'name' => '男',
            ],
            2 => [
                'id' => SexConst::SEX_FEMALE,
                'key' => 'female',
                'name' => '女',
            ],
        ], $consts);
    }

    public function testGetConstName()
    {
        $sex = new SexConst();

        $this->assertEquals('男', $sex->getConstName('sex', SexConst::SEX_MALE));
    }

    public function testGetConstId()
    {
        $sex = new SexConst();

        $this->assertEquals(SexConst::SEX_MALE, $sex->getConstId('sex', 'male'));
    }

    public function testGetConstValue()
    {
        $sex = new SexConst();

        $this->assertEquals('男', $sex->getConstValue('sex', SexConst::SEX_MALE, 'label'));
    }
}
