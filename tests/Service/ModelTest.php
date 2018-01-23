<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

class ModelTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        error_reporting(-1);

        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Plugin\Fixture');

        static::dropTables();

        $table = wei()->testRef()->getTable();
        wei()->schema->table($table)
            ->id()
            ->string('json')
            ->string('mixed')
            ->exec();

        wei()->db->batchInsert($table, [
            [
                'id' => 1,
                'json' => json_encode(['a' => 'b']),
                'mixed' => '["a":"b"]'
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
        wei()->schema->dropIfExists(wei()->testRef()->getTable());
    }

    public function testArrayIncrementOperator()
    {
        $model = $this->getModel();

        $model['id'] = 1;
        $model['id']++;

        $this->assertEquals(2, $model['id']);
    }

    public function testArrayNestedSet()
    {
        $model = $this->getModel();

        $model['mixed'] = [];
        $model['mixed']['next'] = 'a';

        $this->assertEquals('a', $model['mixed']['next']);
    }

    public function testArrayNestedSetWithCast()
    {
        $this->setExpectedException('ErrorException', 'Illegal string offset \'next\'');

        $model = $this->getModel();

        // TODO 需要改造cast逻辑
        $model['json'] = []; // => json等于字符串'[]'
        $model['json']['next'] = 'a';

        $this->assertEquals('a', $model['json']['next']);
    }

    public function testNullArrayIncrementOperator()
    {
        $model = $this->getModel();

        $model['id']++;

        $this->assertEquals(1, $model['id']);
    }

    public function testNullArrayNestedSet()
    {
        $model = $this->getModel();

        $model['json']['next'] = 'a';

        $this->assertEquals('a', $model['json']['next']);
    }

    public function dtestPropIncrementOperator()
    {
        $model = $this->getModel();

        $model->id = 1;
        $model->id++;

        $this->assertEquals(2, $model->test);
        $this->assertEquals(2, $model->toArray()['id']);
    }

    public function dtestNullPropIncrementOperator()
    {
        $model = $this->getModel();

        $model->id++;

        $this->assertEquals(1, $model->id);
        $this->assertEquals(1, $model->toArray()['id']);
    }

    public function dtestPropNestedSet()
    {
        $model = $this->getModel();

        $model->json = [];
        $model->json['next'] = 'a';

        $this->assertEquals('a', $model->json['next']);
        $this->assertEquals('a', $model->toArray()['json']['next']);
    }

    public function dtestNullPropNestedSet()
    {
        $model = $this->getModel();

        $model->json['next'] = 'a';

        $this->assertEquals('a', $model->json['next']);

        $this->assertEquals('a', $model->toArray()['json']['next']);
    }

    public function testSave()
    {
        // TODO 加入 extraKey 处理?
        $this->setExpectedException('PDOException');

        $model = $this->getModel();

        // 会导致 Column 'json' cannot be null
        $model['json'];

        $model->save();
    }

    protected function getModel()
    {
        return wei()->testRef();
    }
}
