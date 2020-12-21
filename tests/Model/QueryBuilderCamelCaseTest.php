<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Service\QueryBuilder as Qb;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\DbTrait;

class QueryBuilderCamelCaseTest extends BaseTestCase
{
    use DbTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::setTablePrefix('p_');
    }

    public static function tearDownAfterClass(): void
    {
        static::dropTables();
        static::resetTablePrefix();
        parent::tearDownAfterClass();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->wei->setConfig('queryBuilder', [
            'dbKeyConverter' => [Qb::class, 'snake'],
            'phpKeyConverter' => [Qb::class, 'camel'],
        ]);
    }

    public function testResult()
    {
        $this->initFixtures();

        $data = Qb::table('test_users')->first();

        $this->assertIsArray($data);
        $this->assertEquals('1', $data['groupId']);
        $this->assertArrayNotHasKey('group_id', $data);
    }

    public function testQueryParts()
    {
        $this->initFixtures();

        $qb = Qb::table('testUsers')->where('groupId', 1);

        $this->assertSame('SELECT * FROM `p_test_users` WHERE `group_id` = 1', $qb->getRawSql());

        $this->assertSame('1', $qb->fetch()['groupId']);
    }

    public function testAllowSnakeCaseQueryParts()
    {
        $this->initFixtures();

        $qb = Qb::table('test_users')->where('group_id', 1);

        $this->assertSame('SELECT * FROM `p_test_users` WHERE `group_id` = 1', $qb->getRawSql());

        $this->assertSame('1', $qb->fetch()['groupId']);
    }
}
