<?php

namespace Miaoxing\Plugin\Traits;

use Miaoxing\Plugin\Test\BaseTestCase;

class SoftDeleteTest extends BaseTestCase
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
        $this->assertEmpty($record['deleted_at']);

        $record->destroy();
        $this->assertNotEmpty($record['deleted_at']);
    }

    public function testRestore()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);

        $record->destroy();
        $record->restore();
        $this->assertEmpty($record['deleted_at']);
    }

    public function testReallyDestroy()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);

        $record->reallyDestroy();
        $this->assertEmpty($record['deleted_at']);

        $record->reload();
        $this->assertNull($record['id']);
    }

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

        $false = wei()->testSoftDelete()->findById($record['id']);
        $this->assertFalse($false);

        $record = wei()->testSoftDelete()->unscoped()->findById($record['id']);
        $this->assertNotFalse($record);
    }

    public function testWithoutDeleted()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);

        $record = wei()->testSoftDelete()->withoutDeleted()->findById($record['id']);
        $this->assertNotFalse($record);

        $record->destroy();
        $record = wei()->testSoftDelete()->withoutDeleted()->findById($record['id']);
        $this->assertFalse($record);
    }

    public function testOnlyDeleted()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);

        $false = wei()->testSoftDelete()->onlyDeleted()->findById($record['id']);
        $this->assertFalse($false);

        $record->destroy();
        $record = wei()->testSoftDelete()->onlyDeleted()->findById($record['id']);
        $this->assertNotFalse($record);
    }

    public function testWithDeleted()
    {
        $record = wei()->testSoftDelete()->save(['name' => __FUNCTION__]);

        $record = wei()->testSoftDelete()->withDeleted()->findById($record['id']);
        $this->assertNotFalse($record);

        $record->destroy();
        $record = wei()->testSoftDelete()->onlyDeleted()->findById($record['id']);
        $this->assertNotFalse($record);
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_soft_deletes');
    }

    public function setUp()
    {
        parent::setUp();

        $this->clearLogs();
    }

    protected function clearLogs()
    {
        // preload fields cache
        wei()->testSoftDelete()->getFields();

        wei()->db->setOption('queries', []);
    }
}
