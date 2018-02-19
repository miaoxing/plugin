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
                'label' => '男',
                'id' => SexConst::SEX_MALE,
                'name' => 'male',
            ],
            2 => [
                'label' => '女',
                'id' => SexConst::SEX_FEMALE,
                'name' => 'female',
            ],
        ], $consts);
    }

    public function testGetConstName()
    {
        $sex = new SexConst();

        $this->assertEquals('male', $sex->getConstName('sex', SexConst::SEX_MALE));
    }

    public function testGetConstIdByName()
    {
        $sex = new SexConst();

        $this->assertEquals(SexConst::SEX_MALE, $sex->getConstIdByName('sex', 'male'));
    }

    public function testGetConstValue()
    {
        $sex = new SexConst();

        $this->assertEquals('男', $sex->getConstValue('sex', SexConst::SEX_MALE, 'label'));
    }
}
