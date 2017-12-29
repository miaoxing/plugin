<?php

namespace MiaoxingTest\Plugin\Traits;

use Miaoxing\Plugin\Test\BaseTestCase;

class DefaultScopeTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::dropTables();
        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Plugin\Fixture');

        wei()->schema->table('test_default_scopes')
            ->id()
            ->string('name')
            ->char('type', 1)
            ->bool('active')
            ->exec();

        wei()->db->insertBatch('test_default_scopes', [
            [
                'name' => 'first',
                'type' => 'A',
                'active' => true,
            ],
            [
                'name' => 'second',
                'type' => 'B',
                'active' => true,
            ],
            [
                'name' => 'third',
                'type' => 'A',
                'active' => false,
            ],
            [
                'name' => 'fourth',
                'type' => 'B',
                'active' => false,
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
        wei()->schema->dropIfExists('test_default_scopes');
    }

    public function testExecuteWithSqlPart()
    {
        $record = wei()->testDefaultScope()->select('id')->fetchAll();

        $this->assertCount(1, $record);
    }

    public function testExecuteWithoutSqlPart()
    {
        $record = wei()->testDefaultScope()->fetchAll();

        $this->assertCount(1, $record);
    }

    public function testExecuteWithWhere()
    {
        $record = wei()->testDefaultScope()->andWhere(['name' => 'first'])->fetchAll();

        $this->assertCount(1, $record);
    }

    public function testGetDefaultScopes()
    {
        $scopes = wei()->testDefaultScope()->getDefaultScopes();

        $this->assertEquals(['active' => true, 'typeA' => true], $scopes);
    }

    public function testUnscopedAll()
    {
        $count = wei()->testDefaultScope()->unscoped()->count();

        $this->assertEquals(4, $count);
    }

    public function testUnscopedOne()
    {
        $count = wei()->testDefaultScope()->unscoped('active')->count();

        $this->assertEquals(2, $count);
    }

    public function testUnscopeMulti()
    {
        $count = wei()->testDefaultScope()->unscoped(['active', 'typeA'])->count();

        $this->assertEquals(4, $count);
    }
}
