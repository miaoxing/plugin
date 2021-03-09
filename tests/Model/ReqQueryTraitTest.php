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
        $query = TestReqQuery::new()->setReq($req)->reqOrderBy();
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_req_queries`',
            'ORDER BY `id` DESC',
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
        $query = TestReqQuery::new()->setReq($req)->reqOrderBy();
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_req_queries`',
            'ORDER BY `start_at` ASC',
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
        $query = TestReqQuery::new()->setReq($req)->reqOrderBy();
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_req_queries`',
            'ORDER BY `start_at` ASC, `id` DESC',
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
        $query = TestReqQuery::new()->setReq($req)->reqOrderBy();
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_req_queries`',
            'ORDER BY `start_at` ASC, `id` DESC',
        ]), $query->getRawSql());
    }

    public function testSetDefaultSortColumn()
    {
        $query = TestReqQuery::new()->setDefaultSortColumn('start_at')->reqOrderBy();
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_req_queries`',
            'ORDER BY `start_at` DESC',
        ]), $query->getRawSql());
    }

    public function testSetDefaultSortColumns()
    {
        $query = TestReqQuery::new()->setDefaultSortColumn(['start_at', 'id'])->reqOrderBy();
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_req_queries`',
            'ORDER BY `start_at` DESC, `id` DESC',
        ]), $query->getRawSql());
    }

    public function testSetDefaultSortColumnAndOrder()
    {
        $query = TestReqQuery::new()->setDefaultSortColumn('start_at', 'ASC')->reqOrderBy();
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_req_queries`',
            'ORDER BY `start_at` ASC',
        ]), $query->getRawSql());
    }

    public function testSetDefaultOrder()
    {
        $query = TestReqQuery::new()->setDefaultOrder('ASC')->reqOrderBy();
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_req_queries`',
            'ORDER BY `id` ASC',
        ]), $query->getRawSql());
    }

    public function testSetDefaultOrders()
    {
        $query = TestReqQuery::new()->setDefaultSortColumn(['id', 'start_at'])->setDefaultOrder(['ASC', 'DESC'])
            ->reqOrderBy();
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_req_queries`',
            'ORDER BY `id` ASC, `start_at` DESC',
        ]), $query->getRawSql());
    }

    public function testGetReqOrderBy()
    {
        $query = TestReqQuery::new()->setReqOrderBy([
            [['id', 'start_at'], ['DESC', 'ASC']],
            ['id', 'DESC'],
            ['id'],
            'id',
            [['id', 'start_at']],
            [['id', 'start_at'], ['DESC']],
        ]);

        $query->addReqOrderBy('id');

        $this->assertSame([
            [['id', 'start_at'], ['DESC', 'ASC']],
            [['id'], ['DESC']],
            [['id']],
            [['id']],
            [['id', 'start_at']],
            [['id', 'start_at'], ['DESC']],
            [['id']],
        ], $query->getReqOrderBy());
    }

    public function testGetReqOrderByContainsInvalidValue()
    {
        $query = TestReqQuery::new()->setReqOrderBy([[]]);

        $this->expectExceptionObject(
            new \RuntimeException('Expected the order by value contains 0-index value, given: []')
        );
        $query->getReqOrderBy();
    }

    public function testGetReqOrderByReturnsFalse()
    {
        $query = TestReqQuery::new()->setReqOrderBy(false);
        $this->assertFalse($query->getReqOrderBy());
    }

    /**
     * @param array $data
     * @param array|false $reqOrderBy
     * @param string $sql
     * @dataProvider providerForSetReqOrderBy
     */
    public function testSetReqOrderBy(array $data, $reqOrderBy, string $sql)
    {
        $req = new Req([
            'fromGlobal' => false,
            'data' => $data,
        ]);
        $query = TestReqQuery::new()
            ->setReq($req)
            ->setReqOrderBy($reqOrderBy)
            ->reqOrderBy();
        $this->assertSame('SELECT * FROM `test_req_queries`' . ($sql ? ' ORDER BY ' . $sql : ''), $query->getRawSql());
    }

    public function providerForSetReqOrderBy(): array
    {
        return [
            [
                [],
                [],
                '`id` DESC', // default
            ],
            [
                [],
                false, // dont add order by
                '',
            ],
            [
                [
                    'sort' => 'id',
                    'order' => 'desc',
                ],
                [],
                '`id` DESC', // default
            ],
            [
                [
                    'sort' => 'custom',
                    'order' => 'asc',
                ],
                [
                    [['start_at'], ['DESC']],
                ],
                '`id` DESC', // not match, fallback to default
            ],
            [
                [
                    'sort' => 'start_at',
                    'order' => 'asc',
                ],
                [
                    [['id']],
                    [['start_at'], ['ASC']], // matched
                ],
                '`start_at` ASC',
            ],
            [
                [
                    'sort' => ['start_at', 'id'],
                ],
                [
                    [['id']],
                    [['start_at', 'id'], ['DESC']], // matched
                ],
                '`start_at` DESC, `id` DESC',
            ],
            [
                [
                    'sort' => ['start_at', 'id'],
                ],
                [
                    [['start_at'], ['ASC']], // order not match
                    [['start_at', 'id'], ['DESC']], // matched
                ],
                '`start_at` DESC, `id` DESC',
            ],
            [
                [
                    'sort' => ['start_at', 'id'],
                    'order' => ['asc', 'asc'],
                ],
                [
                    [['start_at'], ['ASC']],
                    [['start_at', 'id'], [null, 'ASC']], // matched
                ],
                '`start_at` ASC, `id` ASC',
            ],
            [
                [
                    'sort' => ['start_at', 'id'],
                    'order' => ['asc', 'asc'],
                ],
                [
                    [['start_at'], ['ASC']],
                    [['start_at', 'id'], ['', 'ASC']], // matched
                ],
                '`start_at` ASC, `id` ASC',
            ],
        ];
    }

    public function testAddReqOrderByWhenReqOrderByIsFalse()
    {
        $query = TestReqQuery::new()->setReqOrderBy(false)->addReqOrderBy('id');
        $this->assertSame([[['id']]], $query->getReqOrderBy());
    }

    public function testReqSearch()
    {
        $req = new Req([
            'fromGlobal' => false,
            'data' => [
                'search' => [
                    'name' => 'test',
                    'name$ct' => 'test',
                ],
            ],
        ]);
        $query = TestReqQuery::new()->setReq($req)->reqSearch();
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_req_queries` WHERE',
            "`name` = 'test'",
            "AND `name` LIKE '%test%'",
        ]), $query->getRawSql());
    }

    /**
     * @param array $search
     * @param array|false $reqSearch
     * @param string $sql
     * @dataProvider providerForSetReqSearch
     */
    public function testSetReqSearch(array $search, $reqSearch, string $sql)
    {
        $req = new Req([
            'fromGlobal' => false,
            'data' => [
                'search' => $search,
            ],
        ]);
        $query = TestReqQuery::new()
            ->setReq($req)
            ->setReqSearch($reqSearch)
            ->reqSearch();
        $this->assertSame($sql, $query->getRawSql());
    }

    public function providerForSetReqSearch(): array
    {
        return [
            [
                [
                    'name' => 'test',
                    'id' => 1,
                ],
                false,
                'SELECT * FROM `test_req_queries`',
            ],
            [
                [
                    'name' => 'test',
                    'id' => 1,
                ],
                [],
                "SELECT * FROM `test_req_queries` WHERE `name` = 'test' AND `id` = 1",
            ],
            [
                [
                    'name' => 'test',
                    'id' => 1,
                ],
                [
                    'name',
                ],
                "SELECT * FROM `test_req_queries` WHERE `name` = 'test'",
            ],
            [
                [
                    'name:ct' => 'test',
                    'id' => 1,
                ],
                [
                    'name:ct',
                ],
                "SELECT * FROM `test_req_queries` WHERE `name` LIKE '%test%'",
            ],
            [
                [
                    'name$ct' => 'test',
                    'id' => 1,
                ],
                [
                    'name:ct',
                ],
                "SELECT * FROM `test_req_queries` WHERE `name` LIKE '%test%'",
            ],
            [
                [
                    'name' => 'test',
                    'id' => 1,
                ],
                [
                    'name',
                ],
                "SELECT * FROM `test_req_queries` WHERE `name` = 'test'",
            ],
            [
                [
                    'name$ct' => 'test',
                    'detail' => [
                        'test_req_query_id' => 1,
                    ],
                ],
                [
                    'name:ct',
                    'detail' => [
                        'test_req_query_id',
                    ],
                ],
                implode(' ', [
                    'SELECT `test_req_queries`.* FROM `test_req_queries`',
                    'LEFT JOIN `test_req_query_details`',
                    'ON `test_req_query_details`.`test_req_query_id` = `test_req_queries`.`id`',
                    "WHERE `test_req_queries`.`name` LIKE '%test%'",
                    'AND `test_req_query_details`.`test_req_query_id` = 1',
                ]),
            ],
            [
                [
                    'detail' => [
                        'test_req_query_id' => 1,
                    ],
                    'name$ct' => 'test',
                ],
                [
                    'name:ct',
                    'detail' => [
                        'test_req_query_id',
                    ],
                ],
                implode(' ', [
                    'SELECT `test_req_queries`.* FROM `test_req_queries`',
                    'LEFT JOIN `test_req_query_details`',
                    'ON `test_req_query_details`.`test_req_query_id` = `test_req_queries`.`id`',
                    'WHERE `test_req_query_details`.`test_req_query_id` = 1',
                    "AND `test_req_queries`.`name` LIKE '%test%'",
                ]),
            ],
            [
                [
                    'name' => '1',
                    'name:ct' => '1',
                    'name:ge' => '1',
                    'name:le' => '1',
                    'name:gt' => '1',
                    'name:lt' => '1',
                    'name:hs' => '1',
                ],
                [],
                implode(' ', [
                    "SELECT * FROM `test_req_queries` WHERE `name` = '1'",
                    "AND `name` LIKE '%1%'",
                    "AND `name` >= '1'",
                    "AND `name` <= '1'",
                    "AND `name` > '1'",
                    "AND `name` < '1'",
                    "AND `name` != ''",
                ]),
            ],
        ];
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

        $this->assertEquals(implode(' ', [
            'SELECT `test_req_queries`.* FROM `test_req_queries` LEFT JOIN',
            '`test_req_query_details` ON `test_req_query_details`.`test_req_query_id` = `test_req_queries`.`id`',
            'WHERE `test_req_queries`.`name` LIKE ? AND `test_req_query_details`.`name` LIKE ?',
        ]), $query->getSql());

        $this->assertEquals('test', $query[0]['name']);
    }
}
