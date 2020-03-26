<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\QueryBuilder;
use Miaoxing\Plugin\Test\BaseTestCase;
use Miaoxing\Services\Service\ServiceTrait;
use MiaoxingTest\Plugin\Fixture\DbTrait;

/**
 * @property \Wei\Db db
 * @method \Wei\Record db($table = null)
 */
class QueryBuilderTest extends BaseTestCase
{
    use ServiceTrait;
    use DbTrait;

    public function testSelect()
    {
        $sql = wei()->queryBuilder('users')->select('name')->getSql();

        $this->assertEquals('SELECT `name` FROM `users`', $sql);
    }

    public function testSelectMultipleByArray()
    {
        $sql = wei()->queryBuilder('users')->select(['name', 'email'])->getSql();

        $this->assertEquals('SELECT `name`, `email` FROM `users`', $sql);
    }

    public function testSelectMultipleByArguments()
    {
        $sql = wei()->queryBuilder('users')->select('name', 'email')->getSql();

        $this->assertEqualsIgnoringCase('SELECT `name`, `email` FROM `users`', $sql);
    }

    public function testSelectAlias()
    {
        $sql = wei()->queryBuilder('users')->select(['name' => 'user_name'])->getSql();

        $this->assertEquals('SELECT `name` AS `user_name` FROM `users`', $sql);
    }

    public function testDistinct()
    {
        $qb = wei()->queryBuilder('users')->select('name')->distinct();

        $this->assertEquals('SELECT DISTINCT `name` FROM `users`', $qb->getSql());

        $this->assertEquals('SELECT `name` FROM `users`', $qb->distinct(false)->getSql());
    }

    public function testSelectDistinct()
    {
        $sql = wei()->queryBuilder('users')->selectDistinct('name')->getSql();

        $this->assertEquals('SELECT DISTINCT `name` FROM `users`', $sql);
    }

    public function testAddSelect()
    {
        $sql = wei()->queryBuilder('users')->select('name')->select('email')->getSql();

        $this->assertEquals('SELECT `name`, `email` FROM `users`', $sql);
    }

    public function testSelectRaw()
    {
        $sql = wei()->queryBuilder('users')->selectRaw('UPPER(name)')->getSql();

        $this->assertEqualsIgnoringCase('SELECT UPPER(name) FROM `users`', $sql);
    }

    public function testWhereEqual()
    {
        $sql = wei()->queryBuilder('users')->where('name', '=', 'twin')->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin'", $sql);
    }

    public function testWhereEqualShorthand()
    {
        $sql = wei()->queryBuilder('users')->where('name', 'twin')->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin'", $sql);
    }

    public function testWhereArray()
    {
        $sql = wei()->queryBuilder('users')->where([
            ['name', 'twin'],
            ['email', '!=', 'twin@example.com'],
        ])->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' AND `email` != 'twin@example.com'", $sql);
    }

    public function testWhereClosure()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->where(function (QueryBuilder $qb) {
                $qb->where('email', '=', 'twin@example.com')
                    ->orWhere('score', '>', 100);
            })
            ->getRawSql();
        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' AND (`email` = 'twin@example.com' OR `score` > 100)",
            $sql);
    }

    public function testWhereRaw()
    {
        $qb = wei()->queryBuilder('users')->whereRaw('name = ?', 'twin');

        $this->assertEquals("SELECT * FROM `users` WHERE name = 'twin'", $qb->getRawSql());
    }

    public function testOrWhere()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhere('email', '!=', 'twin@example.com')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR `email` != 'twin@example.com'",
            $sql);
    }

    public function testMultipleOrWhere()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhere('email', 'twin@example.com')
            ->orWhere('first_name', '=', 'twin')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR `email` = 'twin@example.com' OR `first_name` = 'twin'",
            $sql);
    }

    public function testOrWhereArray()
    {
        $sql = wei()->queryBuilder('users')->orWhere([
            ['name', 'twin'],
            ['email', 'twin@example.com'],
        ])->getRawSql();
        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR `email` = 'twin@example.com'", $sql);
    }

    public function testOrWhereClosure()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhere(function (QueryBuilder $qb) {
                $qb->where('email', '=', 'twin@example.com')
                    ->orWhere('score', '>', 100);
            })
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR (`email` = 'twin@example.com' OR `score` > 100)",
            $sql);
    }

    public function testOrWhereRaw()
    {
        $qb = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereRaw('email = ?', 'twin@example.com');

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR email = 'twin@example.com'",
            $qb->getRawSql());
    }

    public function testWhereBetween()
    {
        $sql = wei()->queryBuilder('users')->whereBetween('age', [1, 10])->getRawSql();

        $this->assertEquals('SELECT * FROM `users` WHERE `age` BETWEEN 1 AND 10', $sql);
    }

    public function testOrWhereBetween()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereBetween('age', [1, 10])->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR `age` BETWEEN 1 AND 10", $sql);
    }

    public function testWhereNotBetween()
    {
        $sql = wei()->queryBuilder('users')->whereNotBetween('age', [1, 10])->getRawSql();

        $this->assertEquals('SELECT * FROM `users` WHERE `age` NOT BETWEEN 1 AND 10', $sql);
    }

    public function testOrWhereNotBetween()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereNotBetween('age', [1, 10])->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR `age` NOT BETWEEN 1 AND 10", $sql);
    }

    public function testWhereIn()
    {
        $sql = wei()->queryBuilder('users')->whereIn('age', [1, 10])->getRawSql();

        $this->assertEquals('SELECT * FROM `users` WHERE `age` IN (1, 10)', $sql);
    }

    public function testOrWhereIn()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereIn('age', [1, 10])->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR `age` IN (1, 10)", $sql);
    }

    public function testWhereNotIn()
    {
        $sql = wei()->queryBuilder('users')->whereNotIn('age', [1, 10])->getRawSql();

        $this->assertEquals('SELECT * FROM `users` WHERE `age` NOT IN (1, 10)', $sql);
    }

    public function testOrWhereNotIn()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereNotIn('age', [1, 10])->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR `age` NOT IN (1, 10)", $sql);
    }

    public function testWhereNull()
    {
        $sql = wei()->queryBuilder('users')->whereNull('age')->getRawSql();

        $this->assertEquals('SELECT * FROM `users` WHERE `age` IS NULL', $sql);
    }

    public function testOrWhereNull()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereNull('age')->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR `age` IS NULL", $sql);
    }

    public function testWhereNotNull()
    {
        $sql = wei()->queryBuilder('users')->whereNotNULL('age')->getRawSql();

        $this->assertEquals('SELECT * FROM `users` WHERE `age` IS NOT NULL', $sql);
    }

    public function testOrWhereNotNull()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereNotNull('age')->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR `age` IS NOT NULL", $sql);
    }

    public function testWhereDate()
    {
        $sql = wei()->queryBuilder('users')->whereDate('created_at', '2020-02-02')->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE DATE(`created_at`) = '2020-02-02'", $sql);
    }

    public function testOrWhereDate()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereDate('created_at', '2020-02-02')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR DATE(`created_at`) = '2020-02-02'", $sql);
    }

    public function testWhereMonth()
    {
        $sql = wei()->queryBuilder('users')->whereMonth('created_at', '2')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE MONTH(`created_at`) = '2'", $sql);
    }

    public function testOrWhereMonth()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereMonth('created_at', '2')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR MONTH(`created_at`) = '2'", $sql);
    }

    public function testWhereDay()
    {
        $sql = wei()->queryBuilder('users')->whereDay('created_at', '2')->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE DAY(`created_at`) = '2'", $sql);
    }

    public function testOrWhereDay()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereDay('created_at', '2')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR DAY(`created_at`) = '2'", $sql);
    }

    public function testWhereYear()
    {
        $sql = wei()->queryBuilder('users')->whereYear('created_at', '2020')->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE YEAR(`created_at`) = '2020'", $sql);
    }

    public function testOrWhereYear()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereYear('created_at', '2020')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR YEAR(`created_at`) = '2020'", $sql);
    }

    public function testWhereTime()
    {
        $sql = wei()->queryBuilder('users')->whereTime('created_at', '20:20:20')->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE TIME(`created_at`) = '20:20:20'", $sql);
    }

    public function testOrWhereTime()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereTime('created_at', '20:20:20')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR TIME(`created_at`) = '20:20:20'", $sql);
    }

    public function testWhereColumn()
    {
        $sql = wei()->queryBuilder('users')
            ->whereColumn('created_at', 'updated_at')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `created_at` = `updated_at`", $sql);
    }

    public function testOrWhereColumn()
    {
        $sql = wei()->queryBuilder('users')
            ->where('name', 'twin')
            ->orWhereColumn('created_at', 'updated_at')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin' OR `created_at` = `updated_at`", $sql);
    }

    public function testWhereContains()
    {
        $sql = wei()->queryBuilder('users')
            ->whereContains('name', 'twin')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` LIKE '%twin%'", $sql);
    }

    public function testOrWhereContains()
    {
        $sql = wei()->queryBuilder('users')
            ->whereContains('name', 'twin')
            ->orWhereContains('email', 'twin')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` LIKE '%twin%' OR `email` LIKE '%twin%'", $sql);
    }

    public function testWhereNotContains()
    {
        $sql = wei()->queryBuilder('users')
            ->whereNotContains('name', 'twin')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` NOT LIKE '%twin%'", $sql);
    }

    public function testOrWhereNotContains()
    {
        $sql = wei()->queryBuilder('users')
            ->whereNotContains('name', 'twin')
            ->orWhereNotContains('email', 'twin')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` NOT LIKE '%twin%' OR `email` NOT LIKE '%twin%'", $sql);
    }

    public function testOrderBy()
    {
        $sql = wei()->queryBuilder('users')->orderBy('id')->getRawSql();

        $this->assertEquals('SELECT * FROM `users` ORDER BY `id` ASC', $sql);
    }

    public function testOrderByDesc()
    {
        $sql = wei()->queryBuilder('users')->orderBy('id', 'DESC')->getRawSql();

        $this->assertEquals('SELECT * FROM `users` ORDER BY `id` DESC', $sql);
    }

    public function testOrderByMultiple()
    {
        $sql = wei()->queryBuilder('users')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'ASC')
            ->getRawSql();

        $this->assertEquals('SELECT * FROM `users` ORDER BY `created_at` DESC, `id` ASC', $sql);
    }

    public function testAsc()
    {
        $sql = wei()->queryBuilder('users')->asc('id')->getRawSql();

        $this->assertEquals('SELECT * FROM `users` ORDER BY `id` ASC', $sql);
    }

    public function testDesc()
    {
        $sql = wei()->queryBuilder('users')->desc('id')->getRawSql();

        $this->assertEquals('SELECT * FROM `users` ORDER BY `id` DESC', $sql);
    }

    public function testInvalidOrder()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Parameter for "order" must be "ASC" or "DESC".'));

        wei()->queryBuilder('users')->orderBy('id', 'as');
    }

    public function testGroupBy()
    {
        $sql = wei()->queryBuilder('users')->groupBy('group_id')->getRawSql();

        $this->assertEquals('SELECT * FROM `users` GROUP BY `group_id`', $sql);
    }

    public function testGroupByMultiply()
    {
        $sql = wei()->queryBuilder('users')->groupBy('group_id', 'type')->getRawSql();

        $this->assertEquals('SELECT * FROM `users` GROUP BY `group_id`, `type`', $sql);
    }

    public function testHaving()
    {
        $sql = wei()->queryBuilder('users')
            ->groupBy('group_id')
            ->having('id', '>', 1)
            ->getRawSql();

        $this->assertEquals('SELECT * FROM `users` GROUP BY `group_id` HAVING `id` > 1', $sql);
    }

    public function testHavingMultiply()
    {
        $sql = wei()->queryBuilder('users')
            ->groupBy('group_id')
            ->having('id', '>', 1)
            ->having('type', 1)
            ->getRawSql();

        $this->assertEquals('SELECT * FROM `users` GROUP BY `group_id` HAVING `id` > 1 AND `type` = 1', $sql);
    }

    public function testHavingRaw()
    {
        $qb = wei()->queryBuilder('users')->havingRaw('name = ?', 'twin');

        $this->assertEquals("SELECT * FROM `users` HAVING name = 'twin'", $qb->getRawSql());
    }

    public function testOrHaving()
    {
        $sql = wei()->queryBuilder('users')
            ->having('name', 'twin')
            ->orHaving('email', '!=', 'twin@example.com')
            ->getRawSql();

        $this->assertEquals("SELECT * FROM `users` HAVING `name` = 'twin' OR `email` != 'twin@example.com'",
            $sql);
    }

    public function testLimit()
    {
        $sql = wei()->queryBuilder('users')->limit(1)->getRawSql();

        $this->assertEquals('SELECT * FROM `users` LIMIT 1', $sql);
    }

    public function testOffset()
    {
        $sql = wei()->queryBuilder('users')->offset(1)->getRawSql();

        $this->assertEquals('SELECT * FROM `users` OFFSET 1', $sql);
    }

    public function testLimitOffset()
    {
        $sql = wei()->queryBuilder('users')->limit(2)->offset(1)->getRawSql();

        $this->assertEquals('SELECT * FROM `users` LIMIT 2 OFFSET 1', $sql);
    }

    public function testPageLimit()
    {
        $sql = wei()->queryBuilder('users')->page(3)->limit(3)->getRawSql();

        $this->assertEquals('SELECT * FROM `users` LIMIT 3 OFFSET 6', $sql);
    }

    public function testLimitPage()
    {
        $sql = wei()->queryBuilder('users')->limit(3)->page(3)->getRawSql();

        $this->assertEquals('SELECT * FROM `users` LIMIT 3 OFFSET 6', $sql);
    }

    public function testWhen()
    {
        $sql = wei()->queryBuilder('users')->when('twin', function (QueryBuilder $qb, $value) {
            $qb->where('name', $value);
        })->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin'", $sql);
    }

    public function testWhenFalse()
    {
        $sql = wei()->queryBuilder('users')->when(false, function (QueryBuilder $qb, $value) {
            $qb->where('name', $value);
        })->getRawSql();

        $this->assertEquals('SELECT * FROM `users`', $sql);
    }

    public function testWhenDefault()
    {
        $sql = wei()->queryBuilder('users')->when(false, function (QueryBuilder $qb, $value) {
            $qb->where('name', $value);
        }, function (QueryBuilder $qb, $value) {
            $qb->where('type', 0);
        })->getRawSql();

        $this->assertEquals('SELECT * FROM `users` WHERE `type` = 0', $sql);
    }

    public function testUnless()
    {
        $sql = wei()->queryBuilder('users')->unless(false, function (QueryBuilder $qb, $value) {
            $qb->where('name', 'twin');
        })->getRawSql();

        $this->assertEquals("SELECT * FROM `users` WHERE `name` = 'twin'", $sql);
    }

    public function testUnlessTrue()
    {
        $sql = wei()->queryBuilder('users')->unless(true, function (QueryBuilder $qb, $value) {
            $qb->where('name', $value);
        })->getRawSql();

        $this->assertEquals('SELECT * FROM `users`', $sql);
    }

    public function testUnlessDefault()
    {
        $sql = wei()->queryBuilder('users')->unless(true, function (QueryBuilder $qb, $value) {
            $qb->where('name', $value);
        }, function (QueryBuilder $qb, $value) {
            $qb->where('type', 0);
        })->getRawSql();

        $this->assertEquals('SELECT * FROM `users` WHERE `type` = 0', $sql);
    }

    public function testFetch()
    {
        $this->initFixtures();

        $data = wei()->queryBuilder('users')->where('id', 1)->fetch();
        $this->assertIsArray($data);
        $this->assertEquals('1', $data['id']);
    }

    public function testFetchAll()
    {
        $this->initFixtures();

        $data = wei()->queryBuilder('users')->fetchAll();

        $this->assertIsArray($data);
        $this->assertEquals('1', $data[0]['group_id']);
    }
}
