<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestVirtual;

class VirtualTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::dropTables();

        wei()->schema->table('test_virtual')
            ->id()
            ->string('first_name')
            ->string('last_name')
            ->exec();
    }

    public static function tearDownAfterClass(): void
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_virtual');
    }

    public function testGetNew()
    {
        $virtual = TestVirtual::new();

        $this->assertNull($virtual->getVirtualColumnValue());
        $this->assertNull($virtual['virtualColumn']);
        $this->assertNull($virtual->virtualColumn);
    }

    public function testGetAfterSet()
    {
        $virtual = TestVirtual::new();

        $virtual->virtualColumn = 'something';

        $this->assertEquals('something', $virtual->getVirtualColumnValue());
        $this->assertEquals('something', $virtual['virtualColumn']);
        $this->assertEquals('something', $virtual->virtualColumn);
        $this->assertEquals('something', $virtual->get('virtualColumn'));
    }

    public function testOffsetSet()
    {
        $virtual = TestVirtual::new();

        $virtual['virtualColumn'] = 'something';

        $this->assertEquals('something', $virtual->getVirtualColumnValue());
        $this->assertEquals('something', $virtual['virtualColumn']);
        $this->assertEquals('something', $virtual->virtualColumn);
        $this->assertEquals('something', $virtual->get('virtualColumn'));
    }

    public function testSetMethod()
    {
        $virtual = TestVirtual::new();

        $virtual->set('virtualColumn', 'something');

        $this->assertEquals('something', $virtual->getVirtualColumnValue());
        $this->assertEquals('something', $virtual['virtualColumn']);
        $this->assertEquals('something', $virtual->virtualColumn);
        $this->assertEquals('something', $virtual->get('virtualColumn'));
    }

    public function testGetFullName()
    {
        $virtual = TestVirtual::new();

        $virtual->firstName = 'Hello';
        $virtual->lastName = 'World';

        $this->assertEquals('Hello World', $virtual->fullName);
    }

    public function testSetFullName()
    {
        $virtual = TestVirtual::new();

        $virtual->fullName = 'Hello World';

        $this->assertEquals('Hello', $virtual->firstName);
        $this->assertEquals('World', $virtual->lastName);
    }
}
