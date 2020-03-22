<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\QueryBuilder;
use Miaoxing\Plugin\Test\BaseTestCase;
use Miaoxing\Services\Service\ServiceTrait;
use PDO;

/**
 * @property \Wei\Db db
 * @method \Wei\Record db($table = null)
 */
class QueryBuilderTest extends BaseTestCase
{
    use ServiceTrait;

    protected function createTable()
    {
        $db = $this->db;
        $db->query("CREATE TABLE prefix_member_group (id INTEGER NOT NULL AUTO_INCREMENT, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))");
        $db->query("CREATE TABLE prefix_member (id INTEGER NOT NULL AUTO_INCREMENT, group_id INTEGER NOT NULL, name VARCHAR(50) NOT NULL, address VARCHAR(256) NOT NULL, PRIMARY KEY(id))");
        $db->query("CREATE TABLE prefix_post (id INTEGER NOT NULL AUTO_INCREMENT, member_id INTEGER NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))");
        $db->query("CREATE TABLE prefix_tag (id INTEGER NOT NULL AUTO_INCREMENT, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))");
        $db->query("CREATE TABLE prefix_post_tag (post_id INTEGER NOT NULL, tag_id INTEGER NOT NULL)");
    }

    protected function dropTable()
    {
        $db = $this->db;
        $db->query('DROP TABLE IF EXISTS prefix_member_group');
        $db->query('DROP TABLE IF EXISTS prefix_member');
        $db->query('DROP TABLE IF EXISTS prefix_post');
        $db->query('DROP TABLE IF EXISTS prefix_tag');
        $db->query('DROP TABLE IF EXISTS prefix_post_tag');
    }

    public function initFixtures()
    {
        $db = $this->db;

        $db->setOption('tablePrefix', 'prefix_');

        $this->dropTable();
        $this->createTable();

        $db->insert('member_group', array(
            'id' => '1',
            'name' => 'vip',
        ));

        $db->insert('member', array(
            'group_id' => '1',
            'name' => 'twin',
            'address' => 'test',
        ));

        $db->insert('member', array(
            'group_id' => '1',
            'name' => 'test',
            'address' => 'test',
        ));

        $db->insert('post', array(
            'member_id' => '1',
            'name' => 'my first post',
        ));

        $db->insert('post', array(
            'member_id' => '1',
            'name' => 'my second post',
        ));

        $db->insert('tag', array(
            'id' => '1',
            'name' => 'database',
        ));

        $db->insert('tag', array(
            'id' => '2',
            'name' => 'PHP',
        ));

        $db->insert('post_tag', array(
            'post_id' => '1',
            'tag_id' => '1',
        ));

        $db->insert('post_tag', array(
            'post_id' => '1',
            'tag_id' => '2',
        ));

        $db->insert('post_tag', array(
            'post_id' => '2',
            'tag_id' => '1',
        ));
    }

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
}
