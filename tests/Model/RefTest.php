<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestRef;
use PDOException;
use Wei\Request;

/**
 * @internal
 */
final class RefTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::dropTables();

        $table = TestRef::getTable();
        wei()->schema->table($table)
            ->id()
            ->string('json')
            ->string('mixed')
            ->exec();

        wei()->db->batchInsert($table, [
            [
                'id' => 1,
                'json' => json_encode(['a' => 'b']),
                'mixed' => '["a":"b"]',
            ],
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists(TestRef::getTable());
    }

    public function testArrayIncrementOperator()
    {
        $model = $this->getModel();

        $model['id'] = 1;
        ++$model['id'];

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
        $model = $this->getModel();

        $model['json'] = [];
        $model['json']['next'] = 'a';

        $this->assertEquals('a', $model['json']['next']);
    }

    public function testNullArrayIncrementOperator()
    {
        $model = $this->getModel();

        ++$model['id'];

        $this->assertEquals(1, $model['id']);
    }

    public function testNullArrayNestedSet()
    {
        $model = $this->getModel();

        $model['json']['next'] = 'a';

        $this->assertEquals('a', $model['json']['next']);
    }

    public function testPropIncrementOperator()
    {
        $model = $this->getModel();

        $model->id = 1;
        ++$model->id;

        $this->assertEquals(2, $model->id);
        $this->assertEquals(2, $model->toArray()['id']);
    }

    public function testNullPropIncrementOperator()
    {
        $model = $this->getModel();

        ++$model->id;

        $this->assertEquals(1, $model->id);
        $this->assertEquals(1, $model->toArray()['id']);
    }

    public function testPropNestedSet()
    {
        $model = $this->getModel();

        $model->mixed = [];
        $model->mixed['next'] = 'a';

        $this->assertEquals('a', $model->mixed['next']);
        $this->assertEquals('a', $model->toArray()['mixed']['next']);
    }

    public function testNullPropNestedSet()
    {
        $model = $this->getModel();

        $model->json['next'] = 'a';

        $this->assertEquals('a', $model->json['next']);

        $this->assertEquals('a', $model->toArray()['json']['next']);
    }

    public function testArrayGetRefBecomeNull()
    {
        $this->expectException(PDOException::class);

        $model = $this->getModel();

        // 会导致 mixed=null
        $isset = (bool) $model['mixed'];
        $this->assertFalse($isset);

        $model->save();
    }

    public function testArrayCheckRefWontBecomeNull()
    {
        $model = $this->getModel();

        // 获取前判断,不会产生null值
        $isset = isset($model['mixed']);
        $this->assertFalse($isset);

        $model->save();
    }

    public function testPropGetRefBecomeNull()
    {
        $this->expectException(PDOException::class);

        $model = $this->getModel();

        // 会导致 mixed=null
        $isset = (bool) $model->mixed;
        $this->assertFalse($isset);

        $model->save();
    }

    public function testPropCheckRefWontBecomeNull()
    {
        $model = $this->getModel();

        // 获取前判断,不会产生null值
        $isset = isset($model->mixed);
        $this->assertFalse($isset);

        $model->save();
    }

    public function testGetService()
    {
        $model = $this->getModel();

        // 通过&__get获取服务不会返回Only variable references should be returned by reference错误
        // @phpstan-ignore-next-line
        $model->request;

        $this->assertInstanceOf(Request::class, $model->request);
    }

    public function testCollGet()
    {
        $models = TestRef::newColl();

        $models[] = TestRef::new();
        $models[0]->id = 2;

        $models[] = TestRef::new();

        $this->assertInstanceOf(TestRef::class, $models[0]);
        $this->assertEquals(2, $models[0]->id);

        $this->assertInstanceOf(TestRef::class, $models[1]);
    }

    public function testGetInvalid()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: notExists'));

        TestRef::new()->get('notExists');
    }

    public function testSetInvalid()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: notExists'));

        TestRef::set('notExists', 'abc');
    }

    public function testFromArray()
    {
        $ref = TestRef::fromArray([
            'json' => ['a' => 'b'],
            'mixed' => 'mixed',
            'notExists' => 'notExists',
        ]);

        $this->assertEquals(['a' => 'b'], $ref->json);
        $this->assertEquals('mixed', $ref->mixed);
    }

    protected function getModel()
    {
        return TestRef::new();
    }
}
