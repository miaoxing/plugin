<?php

namespace MiaoxingTest\Plugin\Model;

use InvalidArgumentException;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestMutator;

/**
 * @internal
 */
final class MutatorTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::dropTables();

        $table = TestMutator::getTable();
        wei()->schema->table($table)
            ->id()
            ->string('getter')
            ->string('setter')
            ->string('mutator')
            ->string('default_value')
            ->exec();

        wei()->db->batchInsert($table, [
            [
                'getter' => base64_encode('getter'),
                'setter' => base64_encode('setter'),
                'mutator' => base64_encode('mutator'),
            ],
            [
                'getter' => base64_encode('getter'),
                'setter' => base64_encode('setter'),
                'mutator' => base64_encode('mutator'),
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
        wei()->schema->dropIfExists(TestMutator::getTable());
    }

    public function testGet()
    {
        $mutator = TestMutator::find(1);

        $this->assertEquals('getter', $mutator->get('getter'));
    }

    public function testSet()
    {
        $mutator = TestMutator::new();
        $mutator->set('setter', 'abc');

        $data = $mutator->getData();
        $this->assertEquals('YWJj', $data['setter'], '由mutator管理数据');
    }

    public function testMagicGet()
    {
        $this->assertEquals('getter', TestMutator::find(1)->getter);
    }

    public function testMagicSet()
    {
        $mutator = TestMutator::new();
        $mutator->setter = 'abc';

        $data = $mutator->getData();
        $this->assertEquals('YWJj', $data['setter']);
    }

    public function testSetColl()
    {
        $mutators = TestMutator::new();
        $mutators[] = TestMutator::new();

        $this->assertInstanceOf(TestMutator::class, $mutators[0]);
    }

    public function testCreate()
    {
        $mutator = TestMutator::new();
        $mutator->setter = 'abc';

        $mutator->save();

        $data = wei()->db->select($mutator->getTable(), ['id' => $mutator->id]);
        $this->assertEquals(base64_encode('abc'), $data['setter']);
    }

    public function testUpdate()
    {
        $mutator = TestMutator::find(1);
        $mutator->setter = 'bbc';
        $mutator->save();

        $data = wei()->db->select($mutator->getTable(), ['id' => $mutator->id]);
        $this->assertEquals(base64_encode('bbc'), $data['setter']);
    }

    public function testIsChange()
    {
        $mutator = TestMutator::find(1);
        $mutator->setter = 'cbc';

        $this->assertTrue($mutator->isChanged('setter'));
    }

    public function testSetInvalid()
    {
        $this->expectExceptionObject(new InvalidArgumentException('Invalid property: invalid'));

        TestMutator::new()->invalid = 'xx';
    }

    public function testSetService()
    {
        $mutator = TestMutator::new();
        $mutator->cache = wei()->cache;

        $this->assertEquals(wei()->cache, $mutator->cache);
    }

    public function testSetGet()
    {
        $mutator = TestMutator::new();

        // 转换为内部数据
        $mutator->mutator = 'abc';

        // 还原为外部数据
        $this->assertEquals('abc', $mutator->mutator);

        // 转换为别的内外数据
        $mutator->mutator = 'bbc';

        // 还原为别的外部数据
        $this->assertEquals('bbc', $mutator->mutator);
    }

    public function testGetTwice()
    {
        $mutator = TestMutator::new();

        // 转换为内部数据
        $mutator->mutator = 'abc';

        // 还原为外部数据
        $this->assertEquals('abc', $mutator->mutator);

        // 再次获得外部数据
        $this->assertEquals('abc', $mutator->mutator);
    }

    public function testGetterGetTwice()
    {
        $mutator = TestMutator::new();

        $mutator->getter = 'abc';

        // 只有Getter没有Setter,所以返回值是直接对abc做decode
        $this->assertEquals(base64_decode('abc'), $mutator->getter);

        // 再次调用也是一样的结果
        $this->assertEquals(base64_decode('abc'), $mutator->getter);
    }

    public function testFind()
    {
        $mutator = TestMutator::find(2);

        $this->assertEquals('mutator', $mutator->mutator);
        $this->assertEquals('getter', $mutator->getter);
        $this->assertEquals(base64_encode('setter'), $mutator->setter);

        $mutator->mutator = 'mutator2';
        $mutator->getter = base64_encode('getter2');
        $mutator->setter = 'setter2';

        $this->assertEquals('mutator2', $mutator->mutator);
        $this->assertEquals('getter2', $mutator->getter);
        $this->assertEquals(base64_encode('setter2'), $mutator->setter);
    }

    public function testNew()
    {
        $mutator = TestMutator::new();

        $this->assertSame('', $mutator->mutator); // 经 getMutatorAttribute 转换
        $this->assertSame('', $mutator->getter); // 经 getGetterAttribute 转换
        $this->assertNull($mutator->setter);

        $mutator->mutator = 'mutator2';
        $mutator->getter = base64_encode('getter2');
        $mutator->setter = 'setter2';

        $this->assertEquals('mutator2', $mutator->mutator);
        $this->assertEquals('getter2', $mutator->getter);
        $this->assertEquals(base64_encode('setter2'), $mutator->setter);
    }

    public function testSave()
    {
        $mutator = TestMutator::save([
            'mutator' => 'mutator2',
            'getter' => base64_encode('getter2'),
            'setter' => 'setter2',
        ]);

        $this->assertEquals('mutator2', $mutator->mutator);
        $this->assertEquals('getter2', $mutator->getter);
        $this->assertEquals(base64_encode('setter2'), $mutator->setter);

        $mutator->mutator = 'mutator3';
        $mutator->getter = base64_encode('getter3');
        $mutator->setter = 'setter3';

        $this->assertEquals('mutator3', $mutator->mutator);
        $this->assertEquals('getter3', $mutator->getter);
        $this->assertEquals(base64_encode('setter3'), $mutator->setter);
    }

    public function testFindAll()
    {
        $table = TestMutator::getTable();
        wei()->db->batchInsert($table, [
            [
                'getter' => base64_encode('getter'),
                'setter' => base64_encode('setter'),
                'mutator' => base64_encode('mutator'),
            ],
            [
                'getter' => base64_encode('getter'),
                'setter' => base64_encode('setter'),
                'mutator' => base64_encode('mutator'),
            ],
        ]);

        $mutators = TestMutator::desc('id')->limit(2)->all();

        $this->assertEquals('getter', $mutators[0]->getter);
        $this->assertEquals(base64_encode('setter'), $mutators[0]->setter);
        $this->assertEquals('mutator', $mutators[0]->mutator);

        $this->assertEquals('getter', $mutators[1]->getter);
        $this->assertEquals(base64_encode('setter'), $mutators[1]->setter);
        $this->assertEquals('mutator', $mutators[1]->mutator);
    }

    public function testNewModelCallGetAttribute()
    {
        $mutator = TestMutator::new();
        $this->assertEquals('default value', $mutator->defaultValue);
    }
}
