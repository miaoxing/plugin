<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use Wei\Request;

class CamelCaseTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::dropTables();
        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Plugin\Fixture');

        wei()->schema->table('test_camel_cases')
            ->id()
            ->int('test_user_id')
            ->exec();
    }

    public static function tearDownAfterClass()
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_camel_cases');
    }

    public function testFromArray()
    {
        $camelCase = wei()->testCamelCase();

        $camelCase->fromArray([
            'testUserId' => 1,
        ]);

        $this->assertEquals(1, $camelCase['testUserId']);
    }

    public function testFromArrayReqIgnoreSnake()
    {
        $camelCase = wei()->testCamelCase();

        /** @var Request $request */
        $request = wei()->newInstance('request');
        $request->fromArray([
            'test_user_id' => 1,
        ]);
        $camelCase->fromArray($request);

        $this->assertNull($camelCase['testUserId']);
        $this->assertNull($camelCase['test_user_id']);

        $request->fromArray([
            'test_user_id' => 1,
            'testUserId' => 2,
        ]);
        $camelCase->fromArray($request);

        $this->assertEquals(2, $camelCase['testUserId']);
    }

    public function testFromArrayArrayAllowSnake()
    {
        $camelCase = wei()->testCamelCase();

        $camelCase->fromArray([
            'test_user_id' => 1,
        ]);

        $this->assertEquals(1, $camelCase['testUserId']);
        $this->assertEquals(1, $camelCase['test_user_id']);

        $camelCase->fromArray([
            'test_user_id' => 1,
            'testUserId' => 2,
        ]);

        $this->assertEquals(2, $camelCase['testUserId']);
    }

    public function testToArray()
    {
        $camelCase = wei()->testCamelCase();

        $camelCase->fromArray([
            'testUserId' => 1,
        ]);

        $this->assertEquals([
            'testUserId' => 1,
            'id' => null,
        ], $camelCase->toArray());
    }

    public function testGetSet()
    {
        $camelCase = wei()->testCamelCase();

        $this->assertEquals(null, $camelCase['testUserId']);

        $camelCase['testUserId'] = 1;

        $this->assertEquals(1, $camelCase['testUserId']);
    }

    public function testNoExtraKey()
    {
        $camelCase = wei()->testCamelCase();

        $camelCase['testUserId'] = 2;
        $this->assertEquals(2, $camelCase['testUserId']);

        $data = $camelCase->getData();
        $this->assertArrayHasKey('test_user_id', $data);
        $this->assertArrayNotHasKey('testUserId', $data);
    }
}
