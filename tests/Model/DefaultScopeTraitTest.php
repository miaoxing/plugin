<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestDefaultScope;

/**
 * @internal
 */
final class DefaultScopeTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::dropTables();

        wei()->schema->table('test_default_scopes')
            ->id()
            ->string('name')
            ->char('type', 1)
            ->bool('active')
            ->exec();

        wei()->db->batchInsert('test_default_scopes', [
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

    public static function tearDownAfterClass(): void
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
        $record = TestDefaultScope::select('id')->fetchAll();

        $this->assertCount(1, $record);
    }

    public function testExecuteWithoutSqlPart()
    {
        $record = TestDefaultScope::fetchAll();

        $this->assertCount(1, $record);
    }

    public function testExecuteWithWhere()
    {
        $record = TestDefaultScope::where('name', 'first')->fetchAll();

        $this->assertCount(1, $record);
    }

    public function testGetDefaultScopes()
    {
        $scopes = TestDefaultScope::getDefaultScopes();

        $this->assertEquals(['active' => true, 'typeA' => true], $scopes);
    }

    public function testUnscopedAll()
    {
        $count = TestDefaultScope::unscoped()->cnt();

        $this->assertEquals(4, $count);
    }

    public function testUnscopedOne()
    {
        $count = TestDefaultScope::unscoped('active')->cnt();

        $this->assertEquals(2, $count);
    }

    public function testUnscopeMulti()
    {
        $count = TestDefaultScope::unscoped(['active', 'typeA'])->cnt();

        $this->assertEquals(4, $count);
    }
}
