<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestSoftDelete;

/**
 * @internal
 */
final class SoftDeleteTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::dropTables();

        wei()->schema->table('test_soft_deletes')
            ->id()
            ->string('name', 32)
            ->softDeletable()
            ->exec();

        wei()->db->batchInsert('test_soft_deletes', [
            [
                'name' => 'normal',
                'deleted_at' => null,
            ],
            [
                'name' => 'deleted',
                'deleted_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public function testDestroy()
    {
        $record = TestSoftDelete::save(['name' => __FUNCTION__]);
        // @codingStandardsIgnoreStart
        $this->assertEmpty($record->deleted_at);
        // @codingStandardsIgnoreEnd

        $record->destroy();
        // @codingStandardsIgnoreStart
        $this->assertNotEmpty($record->deleted_at);
        // @codingStandardsIgnoreEnd
    }

    public function testRestore()
    {
        $record = TestSoftDelete::save(['name' => __FUNCTION__]);

        $record->destroy();
        $record->restore();
        // @codingStandardsIgnoreStart
        $this->assertEmpty($record->deleted_at);
        // @codingStandardsIgnoreEnd
    }

    public function testReallyDestroy()
    {
        $record = TestSoftDelete::save(['name' => __FUNCTION__]);

        $record->reallyDestroy();
        // @codingStandardsIgnoreStart
        $this->assertEmpty($record->deleted_at);
        // @codingStandardsIgnoreEnd

        $record->reload();
        $this->assertNull($record->id);
    }

    /**
     * @throws \Exception
     */
    public function testIsDeleted()
    {
        $record = TestSoftDelete::save(['name' => __FUNCTION__]);
        $this->assertFalse($record->isDeleted());

        $record->destroy();
        $this->assertTrue($record->isDeleted());
    }

    public function testDefaultScope()
    {
        $record = TestSoftDelete::save(['name' => __FUNCTION__]);
        $record->destroy();

        $false = TestSoftDelete::find($record->id);
        $this->assertNull($false);

        $record = TestSoftDelete::unscoped()->find($record->id);
        $this->assertNotNull($record);
    }

    public function testWithoutDeleted()
    {
        $record = TestSoftDelete::save(['name' => __FUNCTION__]);

        $record = TestSoftDelete::withoutDeleted()->find($record->id);
        $this->assertNotNull($record);

        $record->destroy();
        $record = TestSoftDelete::withoutDeleted()->find($record->id);
        $this->assertNull($record);
    }

    public function testOnlyDeleted()
    {
        $record = TestSoftDelete::save(['name' => __FUNCTION__]);

        $false = TestSoftDelete::onlyDeleted()->find($record->id);
        $this->assertNull($false);

        $record->destroy();
        $record = TestSoftDelete::onlyDeleted()->find($record->id);
        $this->assertNotNull($record);
    }

    public function testWithDeleted()
    {
        $record = TestSoftDelete::save(['name' => __FUNCTION__]);

        $record = TestSoftDelete::withDeleted()->find($record->id);
        $this->assertNotNull($record);

        $record->destroy();
        $record = TestSoftDelete::onlyDeleted()->find($record->id);
        $this->assertNotNull($record);
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_soft_deletes');
    }
}
