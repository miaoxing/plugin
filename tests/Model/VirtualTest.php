<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;

class VirtualTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::dropTables();
        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Plugin\Fixture');

        wei()->schema->table('test_virtual')
            ->id()
            ->string('first_name')
            ->string('last_name')
            ->exec();
    }

    public static function tearDownAfterClass()
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
        $virtual = wei()->testVirtual();

        $this->assertNull($virtual->getVirtualColumnValue());
        $this->assertNull($virtual['virtualColumn']);
        $this->assertNull($virtual->virtualColumn);
    }

    public function testGetAfterSet()
    {
        $virtual = wei()->testVirtual();

        $virtual->virtualColumn = 'something';

        $this->assertEquals('something', $virtual->getVirtualColumnValue());
        $this->assertEquals('something', $virtual['virtualColumn']);
        $this->assertEquals('something', $virtual->virtualColumn);
        $this->assertEquals('something', $virtual->get('virtualColumn'));
    }

    public function testOffsetSet()
    {
        $virtual = wei()->testVirtual();

        $virtual['virtualColumn'] = 'something';

        $this->assertEquals('something', $virtual->getVirtualColumnValue());
        $this->assertEquals('something', $virtual['virtualColumn']);
        $this->assertEquals('something', $virtual->virtualColumn);
        $this->assertEquals('something', $virtual->get('virtualColumn'));
    }

    public function testSetMethod()
    {
        $virtual = wei()->testVirtual();

        $virtual->set('virtualColumn', 'something');

        $this->assertEquals('something', $virtual->getVirtualColumnValue());
        $this->assertEquals('something', $virtual['virtualColumn']);
        $this->assertEquals('something', $virtual->virtualColumn);
        $this->assertEquals('something', $virtual->get('virtualColumn'));
    }

    public function testGetFullName()
    {
        $virtual = wei()->testVirtual();

        $virtual->firstName = 'Hello';
        $virtual->lastName = 'World';

        $this->assertEquals('Hello World', $virtual->fullName);
    }

    public function testSetFullName()
    {
        $virtual = wei()->testVirtual();

        $virtual->fullName = 'Hello World';

        $this->assertEquals('Hello', $virtual->firstName);
        $this->assertEquals('World', $virtual->lastName);
    }
}
