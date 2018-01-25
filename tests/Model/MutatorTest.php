<?php

namespace MiaoxingTest\Plugin\Model;

use InvalidArgumentException;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\TestMutator;

class MutatorTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Plugin\Fixture');

        static::dropTables();

        $table = wei()->testMutator()->getTable();
        wei()->schema->table($table)
            ->id()
            ->string('getter')
            ->string('setter')
            ->string('mutator')
            ->exec();

        wei()->db->batchInsert($table, [
            [
                'getter' => base64_encode('getter'),
                'setter' => base64_encode('setter'),
                'mutator' => base64_encode('mutator'),
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
        wei()->schema->dropIfExists(wei()->testMutator()->getTable());
    }

    public function testGet()
    {
        $mutator = wei()->testMutator()->findById(1);

        $this->assertEquals('getter', $mutator->get('getter'));
    }

    public function testSet()
    {
        $mutator = wei()->testMutator();
        $mutator->set('setter', 'abc');

        $data = $mutator->getData();
        $this->assertEquals('abc', $data['setter'], 'Set不会直接更改数据,保存时才改');
    }

    public function testMagicGet()
    {
        $this->assertEquals('getter', wei()->testMutator()->findById(1)->getter);
    }

    public function testMagicSet()
    {
        $mutator = wei()->testMutator();
        $mutator->setter = 'abc';

        $data = $mutator->getData();
        $this->assertEquals('abc', $data['setter']);
    }

    public function testSetColl()
    {
        $mutators = wei()->testMutator();
        $mutators[] = wei()->testMutator();

        $this->assertInstanceOf(TestMutator::class, $mutators[0]);
    }

    public function testCreate()
    {
        $mutator = wei()->testMutator();
        $mutator->setter = 'abc';

        $mutator->save();

        $data = wei()->db->select($mutator->getTable(), ['id' => $mutator->id]);
        $this->assertEquals(base64_encode('abc'), $data['setter']);
    }

    public function testUpdate()
    {
        $mutator = wei()->testMutator()->findById(1);
        $mutator->setter = 'bbc';
        $mutator->save();

        $data = wei()->db->select($mutator->getTable(), ['id' => $mutator->id]);
        $this->assertEquals(base64_encode('bbc'), $data['setter']);
    }

    public function testIsChange()
    {
        $mutator = wei()->testMutator()->findById(1);
        $mutator->setter = 'cbc';

        $this->assertTrue($mutator->isChanged('setter'));
    }

    public function testSetInvalid()
    {
        $this->setExpectedException(InvalidArgumentException::class, 'Invalid property: invalid');

        wei()->testMutator()->invalid = 'xx';
    }

    public function testSetService()
    {
        $mutator = wei()->testMutator();
        $mutator->cache = wei()->cache;

        $this->assertEquals(wei()->cache, $mutator->cache);
    }

    public function testSetGet()
    {
        $mutator = wei()->testMutator();

        // 转换为内部数据
        $mutator->mutator = 'abc';

        // 还原为外部数据
        $this->assertEquals('abc', $mutator->mutator);

        // 转换为别的内外数据
        $mutator->mutator = 'bbc';

        // 还原为别的外部数据
        $this->assertEquals('bbc', $mutator->mutator);
    }

    public function testFind()
    {
        $mutator = wei()->testMutator()->findById(1);

        $this->assertEquals('mutator', $mutator->mutator);
    }
}
