<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;

class GetSetTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::dropTables();
        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Plugin\Fixture');

        wei()->schema->table('test_get_sets')
            ->id('id')
            ->string('name')
            ->exec();

        wei()->db->batchInsert('test_get_sets', [
            [
                'id' => 1,
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
}
