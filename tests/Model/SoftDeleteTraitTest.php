<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;

class SoftDeleteTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::dropTables();
        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Plugin\Fixture');

        wei()->schema->table('test_soft_deletes')
            ->id()
            ->string('name', 32)
            ->softDeletable()
            ->exec();

        wei()->db->insertBatch('test_soft_deletes', [
            [
                'name' => 'normal',
                'deleted_at' => '',
            ],
            [
                'name' => 'deleted',
                'deleted_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }

    public static function tearDownAfterClass()
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public function testDestroy()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);
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
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);

        $record->destroy();
        $record->restore();
        // @codingStandardsIgnoreStart
        $this->assertEmpty($record->deleted_at);
        // @codingStandardsIgnoreEnd
    }

    public function testReallyDestroy()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);

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
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);
        $this->assertFalse($record->isDeleted());

        $record->destroy();
        $this->assertTrue($record->isDeleted());
    }

    public function testDefaultScope()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);
        $record->destroy();

        $false = wei()->testSoftDelete()->findById($record->id);
        $this->assertFalse($false);

        $record = wei()->testSoftDelete()->unscoped()->findById($record->id);
        $this->assertNotFalse($record);
    }

    public function testWithoutDeleted()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);

        $record = wei()->testSoftDelete()->withoutDeleted()->findById($record->id);
        $this->assertNotFalse($record);

        $record->destroy();
        $record = wei()->testSoftDelete()->withoutDeleted()->findById($record->id);
        $this->assertFalse($record);
    }

    public function testOnlyDeleted()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);

        $false = wei()->testSoftDelete()->onlyDeleted()->findById($record->id);
        $this->assertFalse($false);

        $record->destroy();
        $record = wei()->testSoftDelete()->onlyDeleted()->findById($record->id);
        $this->assertNotFalse($record);
    }

    public function testWithDeleted()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);

        $record = wei()->testSoftDelete()->withDeleted()->findById($record->id);
        $this->assertNotFalse($record);

        $record->destroy();
        $record = wei()->testSoftDelete()->onlyDeleted()->findById($record->id);
        $this->assertNotFalse($record);
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_soft_deletes');
    }
}
