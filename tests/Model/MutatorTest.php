<?php

namespace MiaoxingTest\Plugin\Model;

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

        wei()->db->insertBatch($table, [
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
        $this->assertEquals('getter', wei()->testMutator()->findById(1)->get('getter'));
    }

    public function testSet()
    {
        $mutator = wei()->testMutator();
        $mutator->set('setter', 'abc');

        $data = $mutator->getData();
        $this->assertEquals(base64_encode('abc'), $data['setter']);
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
        $this->assertEquals(base64_encode('abc'), $data['setter']);
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
}
