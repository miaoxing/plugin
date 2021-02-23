<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestReqQuery;
use Wei\Req;

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
            ->datetime('start_at')
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

    public function testOrderBy()
    {
        $req = new Req([
            'fromGlobal' => false,
        ]);
        $query = TestReqQuery::new()->setReq($req)->reqOrderBy()->first();
        $this->assertSame(implode(' ', [
            "SELECT * FROM `test_req_queries`",
            "ORDER BY `id` DESC",
            "LIMIT 1",
        ]), $query->getRawSql());
    }

    public function testOrderByCustom()
    {
        $req = new Req([
            'fromGlobal' => false,
            'data' => [
                'sort' => 'start_at',
                'order' => 'asc',
            ],
        ]);
        $query = TestReqQuery::new()->setReq($req)->reqOrderBy()->first();
        $this->assertSame(implode(' ', [
            "SELECT * FROM `test_req_queries`",
            "ORDER BY `start_at` ASC",
            "LIMIT 1",
        ]), $query->getRawSql());
    }

    public function testOrderByMultiple()
    {
        $req = new Req([
            'fromGlobal' => false,
            'data' => [
                'sort' => ['start_at', 'id'],
                'order' => ['asc', 'desc'],
            ],
        ]);
        $query = TestReqQuery::new()->setReq($req)->reqOrderBy()->first();
        $this->assertSame(implode(' ', [
            "SELECT * FROM `test_req_queries`",
            "ORDER BY `start_at` ASC, `id` DESC",
            "LIMIT 1",
        ]), $query->getRawSql());
    }


    public function testOrderByMultipleWithoutOrder()
    {
        $req = new Req([
            'fromGlobal' => false,
            'data' => [
                'sort' => ['start_at', 'id'],
                'order' => ['asc'],
            ],
        ]);
        $query = TestReqQuery::new()->setReq($req)->reqOrderBy()->first();
        $this->assertSame(implode(' ', [
            "SELECT * FROM `test_req_queries`",
            "ORDER BY `start_at` ASC, `id` DESC",
            "LIMIT 1",
        ]), $query->getRawSql());
    }

    public function testReqSearch()
    {
        $req = new Req([
            'fromGlobal' => false,
            'data' => [
                'search' => [
                    'name' => 'test',
                    'name$eq' => 'test',
                ],
            ],
        ]);
        $query = TestReqQuery::new()->setReq($req)->reqSearch()->first();
        $this->assertSame(implode(' ', [
            "SELECT * FROM `test_req_queries` WHERE",
            "`name` = 'test'",
            "AND `name` = 'test'",
            "LIMIT 1",
        ]), $query->getRawSql());
    }

    public function testLikeJoin()
    {
        $req = new Req([
            'fromGlobal' => false,
            'data' => [
                'search' => [
                    'name$ct' => 'test',
                    'detail' => [
                        'name$ct' => 'detail',
                    ],
                ],
            ],
        ]);
        $query = TestReqQuery::new()->setReq($req)->reqSearch()->all();

        $this->assertEquals('test', $query[0]['name']);

        $this->assertEquals(implode(' ', [
            'SELECT `test_req_queries`.* FROM `test_req_queries` LEFT JOIN',
            '`test_req_query_details` ON `test_req_query_details`.`test_req_query_id` = `test_req_queries`.`id`',
            'WHERE `test_req_queries`.`name` LIKE ? AND `test_req_query_details`.`name` LIKE ?',
        ]), $query->getSql());
    }
}
