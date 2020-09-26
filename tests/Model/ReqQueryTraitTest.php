<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestReqQuery;

/**
 * @internal
 */
final class ReqQueryTraitTest extends BaseTestCase
{
    private static $tablePrefix;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$tablePrefix = wei()->db->getTablePrefix();
        wei()->db->setOption('tablePrefix', '');
        static::dropTables();

        wei()->schema->table('test_req_queries')
            ->id('id')
            ->string('name')
            ->timestamps()
            ->exec();

        wei()->schema->table('test_req_query_details')
            ->id('id')
            ->int('test_req_query_id')
            ->string('name')
            ->timestamps()
            ->exec();

        wei()->db->insert('test_req_queries', [
            'id' => 1,
            'name' => 'test',
        ]);

        wei()->db->insert('test_req_query_details', [
            'id' => 1,
            'test_req_query_id' => 1,
            'name' => 'detail',
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        static::dropTables();
        wei()->db->setOption('tablePrefix', static::$tablePrefix);
        parent::tearDownAfterClass();
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_req_queries');
        wei()->schema->dropIfExists('test_req_query_details');
    }

    public function testLikeJoin()
    {
        wei()->req->fromArray([
            'name' => 'test',
            'detail' => [
                'name' => 'detail',
            ],
        ]);
        $query = TestReqQuery::like(['detail.name', 'name'])
            ->all();

        $this->assertEquals('test', $query[0]['name']);

        $this->assertEquals(implode(' ', [
            'SELECT `test_req_queries`.* FROM `test_req_queries` LEFT JOIN',
            '`test_req_query_details` ON `test_req_query_details`.`test_req_query_id` = `test_req_queries`.`id`',
            'WHERE `test_req_query_details`.`name` LIKE ? AND `test_req_queries`.`name` LIKE ?',
        ]), $query->getSql());
    }
}
