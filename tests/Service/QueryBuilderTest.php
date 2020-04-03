<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\QueryBuilder as Qb;
use Miaoxing\Plugin\Test\BaseTestCase;
use Miaoxing\Services\Service\ServiceTrait;
use MiaoxingTest\Plugin\Fixture\DbTrait;

/**
 * @mixin \DbMixin
 */
class QueryBuilderTest extends BaseTestCase
{
    use ServiceTrait;
    use DbTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->db->setOption('tablePrefix', 'p_');
    }

    public function testSelect()
    {
        $sql = Qb::table('users')->select('name')->getSql();

        $this->assertEquals('SELECT `name` FROM `p_users`', $sql);
    }

    public function testStaticSelect()
    {
        $sql = Qb::select('name')->from('users')->getSql();

        $this->assertEquals('SELECT `name` FROM `p_users`', $sql);
    }

    public function testSelectMultipleByArray()
    {
        $sql = Qb::table('users')->select(['name', 'email'])->getSql();

        $this->assertEquals('SELECT `name`, `email` FROM `p_users`', $sql);
    }

    public function testSelectMultipleByArguments()
    {
        $sql = Qb::table('users')->select('name', 'email')->getSql();

        $this->assertEqualsIgnoringCase('SELECT `name`, `email` FROM `p_users`', $sql);
    }

    public function testSelectAlias()
    {
        $sql = Qb::table('users')->select(['name' => 'user_name'])->getSql();

        $this->assertEquals('SELECT `name` AS `user_name` FROM `p_users`', $sql);
    }

    public function testDistinct()
    {
        $qb = Qb::table('users')->select('name')->distinct();

        $this->assertEquals('SELECT DISTINCT `name` FROM `p_users`', $qb->getSql());

        $this->assertEquals('SELECT `name` FROM `p_users`', $qb->distinct(false)->getSql());
    }

    public function testSelectDistinct()
    {
        $sql = Qb::table('users')->selectDistinct('name')->getSql();

        $this->assertEquals('SELECT DISTINCT `name` FROM `p_users`', $sql);
    }

    public function testAddSelect()
    {
        $sql = Qb::table('users')->select('name')->select('email')->getSql();

        $this->assertEquals('SELECT `name`, `email` FROM `p_users`', $sql);
    }

    public function testSelectRaw()
    {
        $sql = Qb::table('users')->selectRaw('UPPER(name)')->getSql();

        $this->assertEqualsIgnoringCase('SELECT UPPER(name) FROM `p_users`', $sql);
    }

    public function testSelectExcept()
    {
        $this->initFixtures();

        $sql = Qb::table('users')->selectExcept('id')->getSql();

        $this->assertEqualsIgnoringCase('SELECT `group_id`, `name`, `address` FROM `p_users`', $sql);
    }

    public function testWhere()
    {
        $sql = Qb::table('users')->where('name', '=', 'twin')->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin'", $sql);
    }

    public function testWhereEqualShorthand()
    {
        $sql = Qb::table('users')->where('name', 'twin')->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin'", $sql);
    }

    public function testWhereArray()
    {
        $sql = Qb::table('users')->where([
            ['name', 'twin'],
            ['email', '!=', 'twin@example.com'],
        ])->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' AND `email` != 'twin@example.com'", $sql);
    }

    public function testWhereClosure()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->where(function (Qb $qb) {
                $qb->where('email', '=', 'twin@example.com')
                    ->orWhere('score', '>', 100);
            })
            ->getRawSql();
        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' AND (`email` = 'twin@example.com' OR `score` > 100)",
            $sql);
    }

    public function testWhereRaw()
    {
        $this->initFixtures();

        $qb = Qb::table('users')->whereRaw("name = 'twin'");

        $this->assertEquals("SELECT * FROM `p_users` WHERE name = 'twin'", $qb->getRawSql());
        $this->assertEquals('twin', $qb->fetch()['name']);
    }

    public function testWhereRawWithQuestionParam()
    {
        $this->initFixtures();

        $qb = Qb::table('users')->whereRaw('name = ?', 'twin');

        $this->assertEquals("SELECT * FROM `p_users` WHERE name = 'twin'", $qb->getRawSql());
        $this->assertEquals('twin', $qb->fetch()['name']);
    }

    public function testWhereRawWithColonParam()
    {
        $this->initFixtures();

        $qb = Qb::table('users')->whereRaw('group_id = :groupId AND name = :name', [
            'groupId' => 1,
            'name' => 'twin',
        ]);

        $this->assertEquals("SELECT * FROM `p_users` WHERE group_id = 1 AND name = 'twin'", $qb->getRawSql());
        $this->assertEquals('twin', $qb->fetch()['name']);
    }

    public function testOrWhere()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhere('email', '!=', 'twin@example.com')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR `email` != 'twin@example.com'",
            $sql);
    }

    public function testMultipleOrWhere()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhere('email', 'twin@example.com')
            ->orWhere('first_name', '=', 'twin')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR `email` = 'twin@example.com' OR `first_name` = 'twin'",
            $sql);
    }

    public function testOrWhereArray()
    {
        $sql = Qb::table('users')->orWhere([
            ['name', 'twin'],
            ['email', 'twin@example.com'],
        ])->getRawSql();
        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR `email` = 'twin@example.com'", $sql);
    }

    public function testOrWhereClosure()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhere(function (Qb $qb) {
                $qb->where('email', '=', 'twin@example.com')
                    ->orWhere('score', '>', 100);
            })
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR (`email` = 'twin@example.com' OR `score` > 100)",
            $sql);
    }

    public function testOrWhereRaw()
    {
        $qb = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereRaw('email = ?', 'twin@example.com');

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR email = 'twin@example.com'",
            $qb->getRawSql());
    }

    public function testWhereBetween()
    {
        $sql = Qb::table('users')->whereBetween('age', [1, 10])->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` WHERE `age` BETWEEN 1 AND 10', $sql);
    }

    public function testOrWhereBetween()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereBetween('age', [1, 10])->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR `age` BETWEEN 1 AND 10", $sql);
    }

    public function testWhereNotBetween()
    {
        $sql = Qb::table('users')->whereNotBetween('age', [1, 10])->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` WHERE `age` NOT BETWEEN 1 AND 10', $sql);
    }

    public function testOrWhereNotBetween()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereNotBetween('age', [1, 10])->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR `age` NOT BETWEEN 1 AND 10", $sql);
    }

    public function testWhereIn()
    {
        $sql = Qb::table('users')->whereIn('age', [1, 10])->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` WHERE `age` IN (1, 10)', $sql);
    }

    public function testOrWhereIn()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereIn('age', [1, 10])->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR `age` IN (1, 10)", $sql);
    }

    public function testWhereNotIn()
    {
        $sql = Qb::table('users')->whereNotIn('age', [1, 10])->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` WHERE `age` NOT IN (1, 10)', $sql);
    }

    public function testOrWhereNotIn()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereNotIn('age', [1, 10])->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR `age` NOT IN (1, 10)", $sql);
    }

    public function testWhereNull()
    {
        $sql = Qb::table('users')->whereNull('age')->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` WHERE `age` IS NULL', $sql);
    }

    public function testOrWhereNull()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereNull('age')->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR `age` IS NULL", $sql);
    }

    public function testWhereNotNull()
    {
        $sql = Qb::table('users')->whereNotNULL('age')->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` WHERE `age` IS NOT NULL', $sql);
    }

    public function testOrWhereNotNull()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereNotNull('age')->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR `age` IS NOT NULL", $sql);
    }

    public function testWhereDate()
    {
        $sql = Qb::table('users')->whereDate('created_at', '2020-02-02')->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE DATE(`created_at`) = '2020-02-02'", $sql);
    }

    public function testOrWhereDate()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereDate('created_at', '2020-02-02')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR DATE(`created_at`) = '2020-02-02'", $sql);
    }

    public function testWhereMonth()
    {
        $sql = Qb::table('users')->whereMonth('created_at', '2')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE MONTH(`created_at`) = '2'", $sql);
    }

    public function testOrWhereMonth()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereMonth('created_at', '2')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR MONTH(`created_at`) = '2'", $sql);
    }

    public function testWhereDay()
    {
        $sql = Qb::table('users')->whereDay('created_at', '2')->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE DAY(`created_at`) = '2'", $sql);
    }

    public function testOrWhereDay()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereDay('created_at', '2')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR DAY(`created_at`) = '2'", $sql);
    }

    public function testWhereYear()
    {
        $sql = Qb::table('users')->whereYear('created_at', '2020')->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE YEAR(`created_at`) = '2020'", $sql);
    }

    public function testOrWhereYear()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereYear('created_at', '2020')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR YEAR(`created_at`) = '2020'", $sql);
    }

    public function testWhereTime()
    {
        $sql = Qb::table('users')->whereTime('created_at', '20:20:20')->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE TIME(`created_at`) = '20:20:20'", $sql);
    }

    public function testOrWhereTime()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereTime('created_at', '20:20:20')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR TIME(`created_at`) = '20:20:20'", $sql);
    }

    public function testWhereColumn()
    {
        $sql = Qb::table('users')
            ->whereColumn('created_at', 'updated_at')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `created_at` = `updated_at`", $sql);
    }

    public function testOrWhereColumn()
    {
        $sql = Qb::table('users')
            ->where('name', 'twin')
            ->orWhereColumn('created_at', 'updated_at')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' OR `created_at` = `updated_at`", $sql);
    }

    public function testWhereContains()
    {
        $sql = Qb::table('users')
            ->whereContains('name', 'twin')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` LIKE '%twin%'", $sql);
    }

    public function testOrWhereContains()
    {
        $sql = Qb::table('users')
            ->whereContains('name', 'twin')
            ->orWhereContains('email', 'twin')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` LIKE '%twin%' OR `email` LIKE '%twin%'", $sql);
    }

    public function testWhereNotContains()
    {
        $sql = Qb::table('users')
            ->whereNotContains('name', 'twin')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` NOT LIKE '%twin%'", $sql);
    }

    public function testOrWhereNotContains()
    {
        $sql = Qb::table('users')
            ->whereNotContains('name', 'twin')
            ->orWhereNotContains('email', 'twin')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` NOT LIKE '%twin%' OR `email` NOT LIKE '%twin%'",
            $sql);
    }

    public function testOrderBy()
    {
        $sql = Qb::table('users')->orderBy('id')->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` ORDER BY `id` ASC', $sql);
    }

    public function testOrderByDesc()
    {
        $sql = Qb::table('users')->orderBy('id', 'DESC')->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` ORDER BY `id` DESC', $sql);
    }

    public function testOrderByMultiple()
    {
        $sql = Qb::table('users')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'ASC')
            ->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` ORDER BY `created_at` DESC, `id` ASC', $sql);
    }

    public function testAsc()
    {
        $sql = Qb::table('users')->asc('id')->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` ORDER BY `id` ASC', $sql);
    }

    public function testDesc()
    {
        $sql = Qb::table('users')->desc('id')->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` ORDER BY `id` DESC', $sql);
    }

    public function testInvalidOrder()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Parameter for "order" must be "ASC" or "DESC".'));

        Qb::table('users')->orderBy('id', 'as');
    }

    public function testGroupBy()
    {
        $sql = Qb::table('users')->groupBy('group_id')->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` GROUP BY `group_id`', $sql);
    }

    public function testGroupByMultiply()
    {
        $sql = Qb::table('users')->groupBy('group_id', 'type')->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` GROUP BY `group_id`, `type`', $sql);
    }

    public function testHaving()
    {
        $sql = Qb::table('users')
            ->groupBy('group_id')
            ->having('id', '>', 1)
            ->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` GROUP BY `group_id` HAVING `id` > 1', $sql);
    }

    public function testHavingMultiply()
    {
        $sql = Qb::table('users')
            ->groupBy('group_id')
            ->having('id', '>', 1)
            ->having('type', 1)
            ->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` GROUP BY `group_id` HAVING `id` > 1 AND `type` = 1', $sql);
    }

    public function testHavingRaw()
    {
        $qb = Qb::table('users')->havingRaw('name = ?', 'twin');

        $this->assertEquals("SELECT * FROM `p_users` HAVING name = 'twin'", $qb->getRawSql());
    }

    public function testOrHaving()
    {
        $sql = Qb::table('users')
            ->having('name', 'twin')
            ->orHaving('email', '!=', 'twin@example.com')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` HAVING `name` = 'twin' OR `email` != 'twin@example.com'",
            $sql);
    }

    public function testLimit()
    {
        $sql = Qb::table('users')->limit(1)->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` LIMIT 1', $sql);
    }

    public function testOffset()
    {
        $sql = Qb::table('users')->offset(1)->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` OFFSET 1', $sql);
    }

    public function testLimitOffset()
    {
        $sql = Qb::table('users')->limit(2)->offset(1)->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` LIMIT 2 OFFSET 1', $sql);
    }

    public function testPageLimit()
    {
        $sql = Qb::table('users')->page(3)->limit(3)->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` LIMIT 3 OFFSET 6', $sql);
    }

    public function testLimitPage()
    {
        $sql = Qb::table('users')->limit(3)->page(3)->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` LIMIT 3 OFFSET 6', $sql);
    }

    public function testWhen()
    {
        $sql = Qb::table('users')->when('twin', function (Qb $qb, $value) {
            $qb->where('name', $value);
        })->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin'", $sql);
    }

    public function testWhenFalse()
    {
        $sql = Qb::table('users')->when(false, function (Qb $qb, $value) {
            $qb->where('name', $value);
        })->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users`', $sql);
    }

    public function testWhenDefault()
    {
        $sql = Qb::table('users')->when(false, function (Qb $qb, $value) {
            $qb->where('name', $value);
        }, function (Qb $qb, $value) {
            $qb->where('type', 0);
        })->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` WHERE `type` = 0', $sql);
    }

    public function testUnless()
    {
        $sql = Qb::table('users')->unless(false, function (Qb $qb, $value) {
            $qb->where('name', 'twin');
        })->getRawSql();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin'", $sql);
    }

    public function testUnlessTrue()
    {
        $sql = Qb::table('users')->unless(true, function (Qb $qb, $value) {
            $qb->where('name', $value);
        })->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users`', $sql);
    }

    public function testUnlessDefault()
    {
        $sql = Qb::table('users')->unless(true, function (Qb $qb, $value) {
            $qb->where('name', $value);
        }, function (Qb $qb, $value) {
            $qb->where('type', 0);
        })->getRawSql();

        $this->assertEquals('SELECT * FROM `p_users` WHERE `type` = 0', $sql);
    }

    public function testFetch()
    {
        $this->initFixtures();

        $data = Qb::table('users')->where('id', 1)->fetch();
        $this->assertIsArray($data);
        $this->assertEquals('1', $data['id']);
    }

    public function testFetchNoDataReturnsNull()
    {
        $this->initFixtures();

        $data = Qb::table('users')->where('id', -1)->fetch();
        $this->assertNull($data);
    }

    public function testFetchColumn()
    {
        $this->initFixtures();

        $result = Qb::table('users')->selectRaw('COUNT(id)')->fetchColumn();
        $this->assertSame('2', $result);

        $result = Qb::table('users')->where('id', -1)->fetchColumn();
        $this->assertNull($result);
    }

    public function testFetchAll()
    {
        $this->initFixtures();

        $data = Qb::table('users')->fetchAll();

        $this->assertIsArray($data);
        $this->assertEquals('1', $data[0]['group_id']);
    }

    public function testFirst()
    {
        $this->initFixtures();

        $data = Qb::table('users')->where('id', 1)->first();
        $this->assertIsArray($data);
        $this->assertEquals('1', $data['id']);
    }

    public function testAll()
    {
        $this->initFixtures();

        $data = Qb::table('users')->all();

        $this->assertIsArray($data);
        $this->assertEquals('1', $data[0]['group_id']);
    }

    public function testIndexBy()
    {
        $this->initFixtures();

        $users = Qb::table('users')->indexBy('name')->fetchAll();

        $this->assertArrayHasKey('twin', $users);
        $this->assertArrayHasKey('test', $users);
    }

    public function testPluck()
    {
        $this->initFixtures();

        $ids = Qb::table('users')->pluck('id');

        $this->assertSame(['1', '2'], $ids);
    }

    public function testPluckWithIndex()
    {
        $this->initFixtures();

        $ids = Qb::table('users')->pluck('name', 'id');

        $this->assertSame([1 => 'twin', 2 => 'test'], $ids);
    }

    public function testCnt()
    {
        $this->initFixtures();

        $count = Qb::table('users')->cnt();
        $this->assertSame(2, $count);

        $count = Qb::table('users')->where('id', 1)->cnt();
        $this->assertSame(1, $count);
    }

    public function testCntIgnoreLimitOffset()
    {
        $this->initFixtures();

        $count = Qb::table('users')->limit(1)->offset(2)->cnt();

        $this->assertSame(2, $count);
    }

    public function testCountBySubQuery()
    {
        $this->markTestIncomplete('todo refactor');

        $this->initFixtures();

        $count = Qb::table('users')::countBySubQuery();

        $this->assertInternalType('int', $count);
        $this->assertEquals(2, $count);

        $count = User::select('id, name')->limit(1)->offset(2)->countBySubQuery();

        $this->assertInternalType('int', $count);
        $this->assertEquals(2, $count);
    }

    public function testMax()
    {
        $this->initFixtures();

        $max = Qb::table('users')->max('id');

        $this->assertSame('2', $max);
    }

    public function testChunk()
    {
        $this->initFixtures();

        $this->db->batchInsert('users', [
            [
                'group_id' => '1',
                'name' => 'twin',
                'address' => 'test',
            ],
            [
                'group_id' => '1',
                'name' => 'twin',
                'address' => 'test',
            ],
        ]);


        $count = 0;
        $times = 0;
        $result = Qb::table('users')->chunk(2, static function ($data, $page) use (&$count, &$times) {
            $count += count($data);
            $times++;
        });

        $this->assertEquals(4, $count);
        $this->assertEquals(2, $times);
        $this->assertTrue($result);
    }

    public function testUpdate()
    {
        $this->initFixtures();

        $row = Qb::table('users')->update(['address' => 'test address']);
        $this->assertEquals(2, $row);

        $user = Qb::table('users')->where('id', 1)->first();
        $this->assertEquals('test address', $user['address']);
    }


    public function testParameters()
    {
        $this->initFixtures();

        $query = Qb::table('users')
            ->whereRaw('id = :id AND group_id = :groupId')
            ->addParameter([
                'id' => 1,
                'groupId' => 1,
            ]);
        $user = $query->fetch();

        $this->assertEquals(array(
            'id' => 1,
            'groupId' => 1,
        ), $query->getBindParams());

        $this->assertEquals(1, $query->getParameter('id'));
        $this->assertNull($query->getParameter('no'));

        $this->assertEquals(1, $user['id']);
        $this->assertEquals(1, $user['group_id']);

        // TODO set parameter
        $query->removeParameters()->addParameter([
            'id' => 10,
            'groupId' => 1,
        ]);
        $user = $query->first();
        $this->assertNull($user);
    }

    /**
     * @dataProvider providerForParameterValue
     * @param mixed $value
     */
    public function testParameterValue($value)
    {
        $this->initFixtures();

        $query = Qb::table('users')
            ->where('id', $value)
            ->where('id', '=', $value)
            ->orWhere('id', $value)
            ->orWhere('id', '=', $value)
            ->having('id', $value)
            ->having('id', '=', $value)
            ->orHaving('id', $value)
            ->orHaving('id', '=', $value);

        // No error raise
        $array = $query->fetchAll();
        $this->assertIsArray($array);
    }

    public function providerForParameterValue()
    {
        return [
            [null],
            ['0'],
            [0],
            [true],
            [[null]],
        ];
    }


    public function testGetAndResetAllSqlParts()
    {
        $query = Qb::table('users')->offset(1)->limit(1);

        $this->assertEquals(1, $query->getSqlPart('offset'));
        $this->assertEquals(1, $query->getSqlPart('limit'));

        $queryParts = $query->getSqlParts();
        $this->assertArrayHasKey('offset', $queryParts);
        $this->assertArrayHasKey('limit', $queryParts);

        $query->resetSqlParts();

        $this->assertEquals(null, $query->getSqlPart('offset'));
        $this->assertEquals(null, $query->getSqlPart('limit'));
    }

    public function testGetTableFromQueryBuilder()
    {
        $query = Qb::table('users');

        $this->assertEquals('users', $query->getTable());

        $query->from('users u');
        $this->assertEquals('users', $query->getTable());

        $query->from('users AS u');
        $this->assertEquals('users', $query->getTable());
    }

    public function testDeleteRecordByQueryBuilder()
    {
        $this->initFixtures();

        $result = User::where('group_id = ?', 1)->delete();
        $this->assertEquals(2, $result);

        $result = User::delete(array('group_id' => 1));
        $this->assertEquals(0, $result);
    }

    /**
     * @link http://edgeguides.rubyonrails.org/active_record_querying.html#conditions
     */
    public function testQuery()
    {
        $this->markTestSkipped('todo');

        $this->initFixtures();

        // Subset conditions
        $query = User::where(array('group_id' => array('1', '2')));
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member WHERE group_id IN (?, ?) LIMIT 1", $query->getSql());
        $this->assertEquals('1', $user['group_id']);

        $query = User::where(array(
            'id' => '1',
            'group_id' => array('1', '2'),
        ));
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member WHERE id = ? AND group_id IN (?, ?) LIMIT 1",
            $query->getSql());
        $this->assertEquals('1', $user['id']);

        // Overwrite where
        $query = $this
            ->db('users')
            ->where('id = :id')
            ->where('group_id = :groupId')
            ->setParameter('groupId', 1);
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member WHERE group_id = :groupId LIMIT 1", $query->getSql());
        $this->assertEquals('1', $user['group_id']);

        // Join
        $query = $this
            ->db('users')
            ->select('prefix_member.*, prefix_member_group.name AS group_name')
            ->leftJoin('prefix_member_group', 'prefix_member_group.id = prefix_member.group_id');
        $user = $query->fetch();

        $this->assertEquals("SELECT prefix_member.*, prefix_member_group.name AS group_name FROM prefix_member LEFT JOIN prefix_member_group ON prefix_member_group.id = prefix_member.group_id LIMIT 1",
            $query->getSql());
        $this->assertArrayHasKey('group_name', $user);

        // Join with table alias
        $query = $this
            ->db('member u')
            ->rightJoin('prefix_member_group g', 'g.id = u.group_id');

        $this->assertEquals("SELECT * FROM prefix_member u RIGHT JOIN prefix_member_group g ON g.id = u.group_id",
            $query->getSql());
    }
}
