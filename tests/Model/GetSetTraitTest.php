<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;

class GetSetTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::dropTables();
        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Services\Model\Fixture');

        wei()->schema->table('test_get_sets')
            ->id('id')
            ->string('name')
            ->int('user_count')
            ->exec();

        wei()->db->batchInsert('test_get_sets', [
            [
                'id' => 1,
                'name' => 'abc'
            ],
        ]);
    }

    public static function tearDownAfterClass()
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_get_sets');
    }

    public function testIsset()
    {
        $test = wei()->testGetSet();
        $test->id = 2;

        $this->assertTrue(isset($test['id']));
        $this->assertNull($test->name);

        // NOTE 暂时不支持
        $this->assertFalse(isset($test->id));

        // 可直接判断
        $this->assertTrue((bool) $test->id);
    }

    public function testGetIdBecomeNull()
    {
        $test = wei()->testGetSet();
        // receive id
        $test->id;

        $test->save();

        $this->assertNotNull($test->id);

        $this->assertInternalType('int', $test->id);
    }

    public function testSaveIdShouldBeInt()
    {
        $test = wei()->testGetSet();

        $test->save();

        $this->assertInternalType('int', $test->id);
    }

    public function testIndexBy()
    {
        $tests = wei()->testGetSet()
            ->indexBy('name')
            ->findAll(['name' => 'abc']);

        $this->assertEquals('abc', $tests['abc']->name);
    }

    public function testIncrSave()
    {
        $getSet = wei()->testGetSet();
        $getSet->incrSave('userCount', 2);
        $this->assertEquals(2, $getSet->userCount);
        $this->assertFalse($getSet->isChanged('userCount'));
        $this->assertFalse($getSet->isChanged());

        $getSet->incrSave('userCount', 3);
        $getSet->name = 'test';
        $this->assertEquals(5, $getSet->userCount);
        $this->assertFalse($getSet->isChanged('userCount'));
        $this->assertTrue($getSet->isChanged('name'));
        $this->assertTrue($getSet->isChanged());
    }
}
