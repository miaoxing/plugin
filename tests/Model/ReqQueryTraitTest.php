<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestReqQuery;

class ReqQueryTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

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
    }

    public static function tearDownAfterClass(): void
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_req_queries');
        wei()->schema->dropIfExists('test_req_query_details');
    }

    public function testLikeJoin()
    {
        wei()->request->fromArray([
            'name' => '1',
            'detail' => [
                'name' => '2',
            ],
        ]);
        $query = TestReqQuery::like(['detail.name', 'name'])
            ->all();

        $this->assertEquals(
            implode(' ', [
                'SELECT test_req_queries.* FROM test_req_queries',
                'LEFT JOIN test_req_query_details ON test_req_query_details.test_req_query_id = test_req_queries.id',
                'WHERE (test_req_query_details.name LIKE ?) AND (test_req_queries.name LIKE ?)',
            ]),
            $query->getSql()
        );
    }
}
