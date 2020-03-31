<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Model;
use Miaoxing\Plugin\Test\BaseTestCase;
use Miaoxing\Services\Service\ServiceTrait;
use MiaoxingTest\Plugin\Fixture\DbTrait;
use MiaoxingTest\Plugin\Fixture\Model\User;
use MiaoxingTest\Plugin\Fixture\Model\UserGroup;

/**
 * @property \Wei\Db db
 * @method Model db($table = null)
 */
class ModelTest extends BaseTestCase
{
    use ServiceTrait;
    use DbTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->db->addRecordClass('users', User::class);
        $this->db->addRecordClass('user_groups', UserGroup::class);
        $this->db->setOption('tablePrefix', 'p_');
    }

    public function testSetter()
    {
        $this->initFixtures();

        $user = User::find(1);

        $this->assertEquals('1', $user->id);

        $user->id = 2;

        $this->assertEquals('2', $user->id);
    }

    public function testFind()
    {
        $this->initFixtures();

        $user = User::find(1);

        $this->assertSame('SELECT * FROM `p_users` WHERE `id` = 1 LIMIT 1', $user->getRawSql());
        $this->assertEquals('1', $user->id);
    }

    public function testFindOrFail()
    {
        $this->initFixtures();

        $this->expectExceptionObject(new \Exception('Record not found', 404));

        User::findOrFail(99);
    }

    public function testFindNotExistReturnsNull()
    {
        $this->initFixtures();

        $user = User::find('not-exists');

        $this->assertSame('SELECT * FROM `p_users` WHERE `id` = ? LIMIT 1', $this->db->getLastQuery());
        $this->assertNull($user);
    }

    public function testFindNull()
    {
        $this->initFixtures();

        $user = User::find(null);

        $this->assertNull($user);
    }

    public function testFindOrInitAndStatusIsNew()
    {
        $this->initFixtures();

        $user = User::findOrInit(3, [
            'name' => 'name',
        ]);

        $this->assertSame('SELECT * FROM `p_users` WHERE `id` = 3 LIMIT 1', $user->getRawSql());
        $this->assertTrue($user->isNew());
        $this->assertFalse($user->isDestroyed());
    }

    public function testFindOrInitWithSameFields()
    {
        $this->initFixtures();

        // The init data may from request, contains key like id, name
        $user = User::findOrInitBy('id', 3, ['name' => 'name', 'id' => '5']);

        $this->assertSame('SELECT * FROM `p_users` WHERE `id` = 3 LIMIT 1', $user->getRawSql());
        $this->assertEquals(3, $user->id);
        $this->assertEquals('name', $user->name);
    }

    public function testFindAll()
    {
        $this->initFixtures();

        $users = User::findAll([1, 2]);

        $this->assertSame('SELECT * FROM `p_users` WHERE `id` IN (1, 2)', $users->getRawSql());
        $this->assertEquals(2, $users->length());
        $this->assertEquals(1, $users[0]->id);
    }

    public function testFindBy()
    {
        $this->initFixtures();

        $user = User::findBy('name', 'twin');

        $this->assertSame("SELECT * FROM `p_users` WHERE `name` = 'twin' LIMIT 1", $user->getRawSql());
        $this->assertEquals(1, $user->id);
    }

    public function testFindByOperator()
    {
        $this->initFixtures();

        $user = User::findBy('id', '>', 1);

        $this->assertSame('SELECT * FROM `p_users` WHERE `id` > 1 LIMIT 1', $user->getRawSql());
        $this->assertEquals(2, $user->id);
    }

    public function testFindAllBy()
    {
        $this->initFixtures();

        $users = User::findAllBy('id', '>', 1);

        $this->assertSame('SELECT * FROM `p_users` WHERE `id` > 1', $users->getRawSql());
        $this->assertSame('2', $users[0]->id);
        $this->assertSame('test', $users[0]->name);
    }

    public function testFindOrInitBy()
    {
        $this->initFixtures();

        // The init data may from request, contains key like id, name
        $user = User::findOrInitBy(['id' => 3, 'name' => 'tom'], ['name' => 'name', 'id' => '5']);

        $this->assertSame("SELECT * FROM `p_users` WHERE `id` = 3 AND `name` = 'tom' LIMIT 1", $user->getRawSql());
        $this->assertSame(3, $user->id);
        $this->assertSame('name', $user->name);
    }

    public function testFindByOrFail()
    {
        $this->initFixtures();

        $this->expectExceptionObject(new \Exception('Record not found', 404));

        User::findByOrFail('name', 'not-exists');
    }

    public function testFirst()
    {
        $this->initFixtures();

        $user = User::first();

        $this->assertSame('1', $user->id);
    }

    public function testAll()
    {
        $this->initFixtures();

        $users = User::all();

        $this->assertSame(2, $users->length());
    }

    public function testModelSave()
    {
        $this->initFixtures();

        $db = $this->db;

        // Existing member
        $user = User::find(1);
        $user->address = 'address';
        $result = $user->save();

        $this->assertSame($result, $user);
        $this->assertEquals('1', $user->id);

        // New member save with data
        $user = $db->init('users');
        $this->assertTrue($user->isNew());
        $user->fromArray(array(
            'group_id' => '1',
            'name' => 'save',
            'address' => 'save',
        ));
        $result = $user->save();

        $this->assertFalse($user->isNew());
        $this->assertSame($result, $user);
        $this->assertEquals('3', $user->id);
        $this->assertEquals('save', $user->name);

        // Save again
        $user->address = 'address3';
        $result = $user->save();
        $this->assertSame($result, $user);
        $this->assertEquals('3', $user->id);
    }

    public function testModelIsLoaded()
    {
        $this->initFixtures();

        $user = $this->db('users');

        $this->assertFalse($user->isLoaded());

        $user->find(1);

        $this->assertTrue($user->isLoaded());
    }

    public function testQueryBuilder()
    {
        $user = $this->db('users')
            ->where('name', 'twin')
            ->first();

        $this->assertEquals("SELECT * FROM `p_users` WHERE `name` = 'twin' LIMIT 1", $user->getRawSql());
        $this->assertEquals('twin', $user->name);
    }

    /**
     * @link http://edgeguides.rubyonrails.org/active_record_querying.html#conditions
     */
    public function testQuery()
    {
        $this->initFixtures();

        // Pure string conditions
        $query = User::where("name = 'twin'");
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member WHERE name = 'twin' LIMIT 1", $query->getSql());
        $this->assertEquals('twin', $user['name']);

        // ? conditions
        $query = User::where('name = ?', 'twin');
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member WHERE name = ? LIMIT 1", $query->getSql());
        $this->assertEquals('twin', $user['name']);

        $query = User::where('group_id = ? AND name = ?', array('1', 'twin'));
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member WHERE group_id = ? AND name = ? LIMIT 1", $query->getSql());
        $this->assertEquals('1', $user['group_id']);
        $this->assertEquals('twin', $user['name']);

        // : conditions
        $query = User::where('group_id = :groupId AND name = :name', array(
            'groupId' => '1',
            'name' => 'twin',
        ));
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member WHERE group_id = :groupId AND name = :name LIMIT 1",
            $query->getSql());
        $this->assertEquals('1', $user['group_id']);
        $this->assertEquals('twin', $user['name']);

        $query = User::where('group_id = :groupId AND name = :name', array(
            ':groupId' => '1',
            ':name' => 'twin',
        ));
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member WHERE group_id = :groupId AND name = :name LIMIT 1",
            $query->getSql());
        $this->assertEquals('1', $user['group_id']);
        $this->assertEquals('twin', $user['name']);

        // Range conditions
        $query = User::where('group_id BETWEEN ? AND ?', array('1', '2'));
        $this->assertEquals("SELECT * FROM prefix_member WHERE group_id BETWEEN ? AND ?", $query->getSql());

        $user = $query->find();
        $this->assertGreaterThanOrEqual(1, $user['group_id']);
        $this->assertLessThanOrEqual(2, $user['group_id']);

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

        // Where with empty content
        $query = User::where(false);
        $this->assertEquals("SELECT * FROM prefix_member", $query->getSql());

        // Order
        $query = User::orderBy('id', 'ASC');
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member ORDER BY id ASC LIMIT 1", $query->getSql());
        $this->assertEquals('1', $user['id']);

        // Add order
        $query = User::orderBy('id', 'ASC')->addOrderBy('group_id', 'ASC');
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member ORDER BY id ASC, group_id ASC LIMIT 1", $query->getSql());
        $this->assertEquals('1', $user['id']);

        // Select
        $query = User::select('id, group_id');
        $user = $query->fetch();

        $this->assertEquals("SELECT id, group_id FROM prefix_member LIMIT 1", $query->getSql());
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('group_id', $user);
        $this->assertArrayNotHasKey('name', $user);

        // Add select
        $query = User::select('id')->addSelect('group_id');
        $user = $query->fetch();

        $this->assertEquals("SELECT id, group_id FROM prefix_member LIMIT 1", $query->getSql());
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('group_id', $user);
        $this->assertArrayNotHasKey('name', $user);

        // Distinct
        $query = User::select('DISTINCT group_id');
        $user = $query->find();

        $this->assertEquals("SELECT DISTINCT group_id FROM prefix_member LIMIT 1", $query->getSql());
        $this->assertEquals('1', $user['group_id']);

        // Limit
        $query = User::limit(2);
        $user = $query->findAll();

        $this->assertEquals("SELECT * FROM prefix_member LIMIT 2", $query->getSql());
        $this->assertCount(2, $user);

        // Offset
        $query = User::limit(1)->offset(1);
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member LIMIT 1 OFFSET 1", $query->getSql());
        $this->assertEquals(2, $user['id']);

        // Page
        $query = User::page(3);
        $this->assertEquals("SELECT * FROM prefix_member LIMIT 10 OFFSET 20", $query->getSql());

        // Mixed limit and page
        $query = User::limit(3)->page(3);
        $this->assertEquals("SELECT * FROM prefix_member LIMIT 3 OFFSET 6", $query->getSql());

        // Group by
        $query = User::groupBy('id, group_id');
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member GROUP BY id, group_id LIMIT 1", $query->getSql());
        $this->assertEquals('1', $user['group_id']);

        // Having
        $query = User::groupBy('id, group_id')->having('group_id >= ?', '1');
        $user = $query->find();

        $this->assertEquals("SELECT * FROM prefix_member GROUP BY id, group_id HAVING group_id >= ? LIMIT 1",
            $query->getSql());
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

    public function testIndexBy()
    {
        $this->initFixtures();

        $users = $this->db('users')
            ->indexBy('name')
            ->fetchAll();

        $this->assertArrayHasKey('twin', $users);
        $this->assertArrayHasKey('test', $users);

        $users = $this->db('users')
            ->indexBy('name')
            ->findAll();

        $this->assertInstanceOf('\Wei\Record', $users['twin']);
        $this->assertInstanceOf('\Wei\Record', $users['test']);

        $users = $users->toArray();

        $this->assertArrayHasKey('twin', $users);
        $this->assertArrayHasKey('test', $users);
    }

    public function testIndexByMoreThanOneTime()
    {
        $this->initFixtures();

        $users = $this->db('users')
            ->indexBy('id')
            ->findAll();

        $this->assertArrayHasKey(1, $users);

        $users->indexBy('name');
        $this->assertArrayHasKey('twin', $users);

        $users->indexBy('id');
        $this->assertArrayHasKey(1, $users);
    }

    public function testFixUndefinedOffset0WhenFetchEmptyData()
    {
        $this->initFixtures();

        $emptyMembers = User::where(array('group_id' => '3'))->indexBy('id')->fetchAll();
        $this->assertEmpty($emptyMembers);
    }

    public function testRealTimeIndexBy()
    {
        $this->initFixtures();

        $users = User::findAll();

        $users = $users->indexBy('name')->toArray();

        $this->assertArrayHasKey('twin', $users);
        $this->assertArrayHasKey('test', $users);
    }

    public function testQueryUpdate()
    {
        $this->initFixtures();

        $user = User::where('id = 1');
        $result = $user->update("name = 'twin2'");

        $this->assertGreaterThan(0, $result);
        $this->assertEquals("UPDATE prefix_member SET name = 'twin2' WHERE id = 1", $user->getSql());

        $user = $this->db->find('users', 1);
        $this->assertEquals(1, $result);
        $this->assertEquals('twin2', $user['name']);
    }

    public function testBindValue()
    {
        $this->initFixtures();

        // Not array parameter
        $user = $this->db->fetch("SELECT * FROM prefix_member WHERE id = ?", 1, PDO::PARAM_INT);

        $this->assertEquals('1', $user['id']);

        // Array parameter
        $user = $this->db->fetch("SELECT * FROM prefix_member WHERE id = ?", array(1), array(PDO::PARAM_INT));

        $this->assertEquals('1', $user['id']);

        $user = $this->db->fetch("SELECT * FROM prefix_member WHERE id = ? AND group_id = ?", array(1, 1), array(
            PDO::PARAM_INT // (no parameter type for second placeholder)
        ));

        $this->assertEquals('1', $user['id']);
        $this->assertEquals('1', $user['group_id']);

        // Name parameter
        $user = $this->db->fetch("SELECT * FROM prefix_member WHERE id = :id", array(
            'id' => 1,
        ), array(
            'id' => PDO::PARAM_INT,
        ));

        $this->assertEquals('1', $user['id']);

        // Name parameter with colon
        $user = $this->db->fetch("SELECT * FROM prefix_member WHERE id = :id", array(
            'id' => 1,
        ), array(
            ':id' => PDO::PARAM_INT,
        ));

        $this->assertEquals('1', $user['id']);

        $user = $this->db->fetch("SELECT * FROM prefix_member WHERE id = :id", array(
            'id' => '1',
        ));

        $this->assertEquals('1', $user['id']);
    }

    public function testFetchColumn()
    {
        $this->initFixtures();

        $count = $this->db->fetchColumn("SELECT COUNT(id) FROM prefix_member");
        $this->assertEquals(2, $count);
    }

    public function testRecordNamespace()
    {
        $this->initFixtures();

        $this->db->setOption('recordNamespace', 'WeiTest\Db');

        $user = $this->db->find('users', 1);

        $this->assertEquals('WeiTest\Db\User', $this->db->getRecordClass('users'));
        $this->assertInstanceOf('WeiTest\Db\User', $user);
    }

    public function testCustomRecordClass()
    {
        $this->initFixtures();

        $this->db->setOption('recordClasses', array(
            'users' => 'WeiTest\Db\User',
        ));

        $user = $this->db->find('users', 1);

        $this->assertEquals('WeiTest\Db\User', $this->db->getRecordClass('users'));
        $this->assertInstanceOf('WeiTest\Db\User', $user);
    }

    public function testRecordToArray()
    {
        $this->initFixtures();

        $user = $this->db->find('users', 1)->toArray();

        $this->assertInternalType('array', $user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('group_id', $user);
        $this->assertArrayHasKey('name', $user);
        $this->assertArrayHasKey('address', $user);

        $user = $this->db->find('users', 1)->toArray(array('id', 'group_id'));
        $this->assertInternalType('array', $user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('group_id', $user);
        $this->assertArrayNotHasKey('name', $user);
        $this->assertArrayNotHasKey('address', $user);

        $user = $this->db->find('users', 1)->toArray(array('id', 'group_id', 'notExistField'));
        $this->assertInternalType('array', $user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('group_id', $user);
        $this->assertArrayNotHasKey('name', $user);
        $this->assertArrayNotHasKey('address', $user);

        $user = $this->db->init('users')->toArray();
        $this->assertInternalType('array', $user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('group_id', $user);
        $this->assertArrayHasKey('name', $user);
        $this->assertArrayHasKey('address', $user);
        $this->assertNull($user['id']);
        $this->assertNull($user['group_id']);
        $this->assertNull($user['name']);
        $this->assertNull($user['address']);

        $users = User::findAll()->toArray(array('id', 'group_id'));
        $this->assertInternalType('array', $users);
        $this->assertArrayHasKey(0, $users);
        $this->assertArrayHasKey('id', $users[0]);
        $this->assertArrayHasKey('group_id', $users[0]);
        $this->assertArrayNotHasKey('name', $users[0]);

        $this->db->setOption('recordClasses', array(
            'users' => 'WeiTest\Db\User',
        ));
    }

    public function testNewRecordToArrayWithoutReturnFields()
    {
        $this->initFixtures();

        $user = User::findOrInit(array('id' => 9999));

        $this->assertTrue($user->isNew());

        $data = $user->toArray();

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('group_id', $data);
        $this->assertArrayHasKey('name', $data);
    }

    public function testNewRecordToArrayWithReturnFields()
    {
        $this->initFixtures();

        $user = User::findOrInit(array('id' => 9999));

        $this->assertTrue($user->isNew());

        $data = $user->toArray(array('group_id', 'name'));

        $this->assertArrayNotHasKey('id', $data);
        $this->assertArrayHasKey('group_id', $data);
        $this->assertArrayHasKey('name', $data);
    }

    public function testToJson()
    {
        $this->initFixtures();
        $user = $this->db->init('users');
        $this->assertInternalType('string', $user->toJson());
    }

    public function testDestroyRecord()
    {
        $this->initFixtures();

        $user = $this->db->find('users', 1);

        $result = $user->destroy();

        $this->assertInstanceOf('\Wei\Record', $result);

        $user = $this->db->find('users', 1);

        $this->assertFalse($user);
    }

    public function testDestroyByCondition()
    {
        $this->initFixtures();

        $result = User::destroy(2);

        $this->assertFalse(User::find(2));
    }

    public function testGetTable()
    {
        $this->initFixtures();

        $user = User::find('1');

        $this->assertEquals('users', $user->getTable());
    }

    public function testFieldNotFound()
    {
        $this->initFixtures();

        $user = User::find('1');

        $this->setExpectedException('\InvalidArgumentException',
            'Field "notFound" not found in record class "Wei\Record"');

        $user['notFound'];
    }

    public function testCollection()
    {
        $this->initFixtures();

        $users = $this->db->findAll('users');

        $this->assertInstanceOf('\Wei\Record', $users);

        // ToArray
        $userArray = $users->toArray();
        $this->assertInternalType('array', $userArray);
        foreach ($userArray as $user) {
            $this->assertInternalType('array', $user);
        }

        // Filter
        $firstGroupMembers = $users->filter(function ($user) {
            if ('1' == $user['group_id']) {
                return true;
            } else {
                return false;
            }
        });

        $this->assertEquals('1', $firstGroupMembers[0]['group_id']);
        $this->assertInstanceOf('\Wei\Record', $firstGroupMembers);
        $this->assertNotSame($users, $firstGroupMembers);
    }

    public function testFilter()
    {
        $this->initFixtures();

        $this->db->setOption('recordNamespace', 'WeiTest\Db');
        $users = User::findAll();

        $oneMembers = $users->filter(function ($user) {
            return $user['id'] == 1;
        });

        $this->assertEquals(1, $oneMembers->length());
        $this->assertEquals(1, $oneMembers[0]['id']);

        $noMembers = $users->filter(function () {
            return false;
        });

        $this->assertEquals(0, $noMembers->length());
        $this->assertEmpty($noMembers->toArray());
    }

    public function testRecordUnset()
    {
        $this->initFixtures();

        $user = User::find('1');

        $this->assertEquals('twin', $user['name']);
        $this->assertEquals('1', $user['group_id']);

        unset($user['name']);
        $user->remove('group_id');

        $this->assertEquals(null, $user['name']);
        $this->assertEquals(null, $user['group_id']);
    }

    public function testErrorCodeAndInfo()
    {
        $this->db->errorCode();
        $info = $this->db->errorInfo();

        $this->assertArrayHasKey(0, $info);
        $this->assertArrayHasKey(1, $info);
        $this->assertArrayHasKey(1, $info);
    }

    public function testBeforeAndAfterQuery()
    {
        $this->initFixtures();

        $this->expectOutputRegex('/beforeQueryafterQuery/');

        $this->db->setOption(array(
            'beforeQuery' => function () {
                echo 'beforeQuery';
            },
            'afterQuery' => function () {
                echo 'afterQuery';
            },
        ));

        $this->db->find('users', 1);
    }

    public function testBeforeAndAfterQueryForUpdate()
    {
        $this->initFixtures();

        $this->expectOutputString('beforeQueryafterQuery');

        $this->db->setOption(array(
            'beforeQuery' => function () {
                echo 'beforeQuery';
            },
            'afterQuery' => function () {
                echo 'afterQuery';
            },
        ));

        $this->db->executeUpdate("UPDATE prefix_member SET name = 'twin2' WHERE id = 1");

        $this->assertEquals("UPDATE prefix_member SET name = 'twin2' WHERE id = 1", $this->db->getLastQuery());
    }

    public function testException()
    {
        $this->setExpectedException('PDOException');

        $this->db->query("SELECT * FROM noThis table");
    }

    public function testExceptionWithParams()
    {
        $this->setExpectedException('PDOException',
            'An exception occurred while executing "SELECT * FROM noThis table WHERE id = ?"');

        $this->db->query("SELECT * FROM noThis table WHERE id = ?", array(1));
    }

    public function testUpdateWithoutParameters()
    {
        $this->initFixtures();

        $result = $this->db->executeUpdate("UPDATE prefix_member SET name = 'twin2' WHERE id = 1");

        $this->assertEquals(1, $result);
    }

    public function testCount()
    {
        $users = User::limit(1)->all();

        $this->assertCount(1, $users);
    }

    public function testCountBySubQuery()
    {
        $this->initFixtures();

        $count = User::countBySubQuery();

        $this->assertInternalType('int', $count);
        $this->assertEquals(2, $count);

        $count = User::select('id, name')->limit(1)->offset(2)->countBySubQuery();

        $this->assertInternalType('int', $count);
        $this->assertEquals(2, $count);
    }

    public function testCountWithCondition()
    {
        $this->initFixtures();

        $count = User::count(1);
        $this->assertInternalType('int', $count);
        $this->assertEquals(1, $count);

        $count = User::count(array('id' => 1));
        $this->assertInternalType('int', $count);
        $this->assertEquals(1, $count);
    }

    public function testParameters()
    {
        $this->initFixtures();

        $db = $this->db;

        $query = $db('users')
            ->where('id = :id AND group_id = :groupId')
            ->setParameters(array(
                'id' => 1,
                'groupId' => 1,
            ), array(
                PDO::PARAM_INT,
                PDO::PARAM_INT,
            ));
        $user = $query->find();

        $this->assertEquals(array(
            'id' => 1,
            'groupId' => 1,
        ), $query->getParameters());

        $this->assertEquals(1, $query->getParameter('id'));
        $this->assertNull($query->getParameter('no'));

        $this->assertEquals(1, $user['id']);
        $this->assertEquals(1, $user['group_id']);

        // Set parameter
        $query->setParameter('id', 1, PDO::PARAM_STR);
        $user = $query->find();
        $this->assertEquals(1, $user['id']);

        $query->setParameter('id', 10);
        $user = $query->find();
        $this->assertFalse($user);

        $query = $this
            ->db('users')
            ->andWhere('id = ?', '1', PDO::PARAM_INT);

        $user = $query->find();
        $this->assertEquals('1', $user['id']);
    }

    /**
     * @dataProvider providerForParameterValue
     */
    public function testParameterValue($value)
    {
        $this->initFixtures();

        $query = $this
            ->db('users')
            ->where('id = ?', $value)
            ->andWhere('id = ?', $value)
            ->andWhere('id = ?', $value)
            ->orWhere('id = ?', $value)
            ->orWhere('id = ?', $value)
            ->groupBy('id')
            ->having('id = ?', $value)
            ->andHaving('id = ?', $value)
            ->andHaving('id = ?', $value)
            ->orHaving('id = ?', $value)
            ->orHaving('id = ?', $value);

        // No error raise
        $array = $query->fetchAll();
        $this->assertInternalType('array', $array);
    }

    public function providerForParameterValue()
    {
        return array(
            array('0'),
            array(0),
            array(null),
            array(true),
            array(array(null)),
        );
    }

    public function testGetAndResetAllSqlParts()
    {
        $query = User::offset(1)->limit(1);

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
        $qb = $this->db('users');
        $this->assertEquals('users', $qb->getTable());

        $qb->from('member m');
        $this->assertEquals('users', $qb->getTable());

        $qb->from('member m');
        $this->assertEquals('users', $qb->getTable());

        $qb->from('member AS m');
        $this->assertEquals('users', $qb->getTable());
    }

    public function testDbCount()
    {
        $this->initFixtures();

        $db = $this->db;

        $count = $db->count('users');
        $this->assertInternalType('int', $count);
        $this->assertEquals(2, $count);

        $count = $db->count('users', array('id' => '1'));
        $this->assertInternalType('int', $count);
        $this->assertEquals(1, $count);

        $count = $db->count('users', array('id' => '1'));
        $this->assertInternalType('int', $count);
        $this->assertEquals(1, $count);

        $count = $db->count('users', array('id' => '123'));
        $this->assertInternalType('int', $count);
        $this->assertEquals(0, $count);
    }

    public function testTablePrefix()
    {
        $this->initFixtures();

        $db = $this->db;

        $db->setOption('tablePrefix', 'tbl_');
        $this->assertEquals('tbl_member', $db->getTable('users'));

        $db->setOption('tablePrefix', 'prefix_post_');
        $this->assertEquals(3, $db->count('tag'));
    }

    public function testConnectFails()
    {
        $this->setExpectedException('\PDOException');
        $test = &$this;
        $db = new \Wei\Db(array(
            'wei' => $this->wei,
            'driver' => 'mysql',
            'host' => '255.255.255.255',
            'dbname' => 'test',
            'connectFails' => function ($db, $exception) use ($test) {
                $test->assertTrue(true);
                $test->assertInstanceOf('PDOException', $exception);
            },
        ));
        $db->connect();
    }

    public function testGlobalOption()
    {
        $cb = function () {
        };
        $this->wei->setConfig(array(
            // sqlite
            'db' => array(
                'beforeConnect' => $cb,
            ),
            'mysql.db' => array(
                'beforeConnect' => $cb,
            ),
            'pgsql.db' => array(
                'beforeConnect' => $cb,
            ),
            'cb.db' => array(
                'db' => $this->db,
                'global' => true,
            ),
        ));

        $this->assertSame($cb, $this->db->getOption('beforeConnect'));
        $this->assertSame($cb, $this->cbDb->getOption('beforeConnect'));

        // Remove all relation configuration
        unset($this->cbDb);
        $this->wei->remove('cbDb');
        $this->wei->setConfig('cb.db', array(
            'db' => null,
        ));
    }

    public function testUnsupportedDriver()
    {
        $this->setExpectedException('\RuntimeException', 'Unsupported database driver: abc');

        $db = new \Wei\Db(array(
            'wei' => $this->wei,
            'driver' => 'abc',
        ));

        $db->query("SELECT MAX(1, 2)");
    }

    public function testCustomDsn()
    {
        $db = new \Wei\Db(array(
            'wei' => $this->wei,
            'dsn' => 'sqlite::memory:',
        ));

        $this->assertEquals('sqlite::memory:', $db->getDsn());
    }

    public function testInsertBatch()
    {
        $this->initFixtures();

        $result = $this->db->batchInsert('users', array(
            array(
                'group_id' => '1',
                'name' => 'twin',
                'address' => 'test',
            ),
            array(
                'group_id' => '1',
                'name' => 'test',
                'address' => 'test',
            ),
        ));

        $this->assertEquals(2, $result);
    }

    public function testSlaveDb()
    {
        // Generate slave db configuration name
        $driver = $this->db->getDriver();
        $configName = $driver . 'Slave.db';

        // Set configuration for slave db
        $options = $this->wei->getConfig('db');
        $this->wei->setConfig($configName, $options);

        $this->db->setOption('slaveDb', $configName);

        $query = "SELECT 1 + 2";
        $this->db->query($query);

        // Receives the slave db wei
        /** @var $slaveDb \Wei\Db */
        $slaveDb = $this->wei->get($configName);

        // Test that the query is execute by slave db, not the master db
        $this->assertNotContains($query, $this->db->getQueries());
        $this->assertContains($query, $slaveDb->getQueries());
    }

    public function testReload()
    {
        $this->initFixtures();

        /** @var $user User */
        $user = $this->db->find('users', 1);
        /** @var $user2 User */
        $user2 = $this->db->find('users', 1);

        $user->groupId = 2;
        $user->save();

        $this->assertNotEquals($user->groupId, $user2->groupId);
        $this->assertEquals(1, $user->getLoadTimes());

        $user2->reload();
        $this->assertEquals($user->groupId, $user2->groupId);
        $this->assertEquals(2, $user2->getLoadTimes());
    }

    public function testFindOne()
    {
        $this->initFixtures();

        $record = $this->db->findOne('users', 1);
        $this->assertInstanceOf('\Wei\Record', $record);
    }

    public function testFindOneWithException()
    {
        $this->initFixtures();

        $this->setExpectedException('Exception', 'Record not found', 404);

        $this->db->findOne('users', 999);
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

        $user = User::new();

        $count = 0;
        $times = 0;
        $result = $user->chunk(2, function (User $users, $page) use (&$count, &$times) {
            $count += count($users);
            $times++;
        });

        $this->assertEquals(4, $count);
        $this->assertEquals(2, $times);
        $this->assertTrue($result);
    }

    public function testChunkBreak()
    {
        $this->initFixtures();

        /** @var User $user */
        $user = $this->db->init('users');

        $count = 0;
        $times = 0;
        $result = $user->chunk(1, function (User $users, $page) use (&$count, &$times) {
            $count += $users->length();
            $times++;
            return false;
        });

        $this->assertEquals(1, $count);
        $this->assertEquals(1, $times);
        $this->assertFalse($result);
    }

    public function testIsChanged()
    {
        $this->initFixtures();

        $user = User::new();
        $this->assertFalse($user->isChanged());

        $user->name = 'tt';
        $user->groupId = '1';
        $user->address = 'address';
        $this->assertFalse($user->isChanged('id'));
        $this->assertTrue($user->isChanged('name'));
        $this->assertTrue($user->isChanged());

        $this->assertNull($user->getChangedData('name'));

        $user->name = 'aa';
        $this->assertTrue($user->isChanged());
        $this->assertEquals('tt', $user->getChangedData('name'));

        $user->save();
        $this->assertFalse($user->isChanged());
        $this->assertEmpty($user->getChangedData());
    }

    public function testReconnect()
    {
        $this->db->connect();
        $pdo = $this->db->getOption('pdo');

        $this->db->reconnect();
        $newPdo = $this->db->getOption('pdo');

        $this->assertEquals($pdo, $newPdo);
        $this->assertNotSame($pdo, $newPdo);
    }

    public function testGetter()
    {
        wei(array(
            'test.db' => array(
                'user' => 'user',
                'password' => 'password',
                'host' => 'host',
                'port' => 'port',
                'dbname' => 'dbname',
            ),
        ));

        /** @var $testDb \Wei\Db */
        $testDb = $this->testDb;

        $this->assertEquals('user', $testDb->getUser());
        $this->assertEquals('password', $testDb->getPassword());
        $this->assertEquals('host', $testDb->getHost());
        $this->assertEquals('port', $testDb->getPort());
        $this->assertEquals('dbname', $testDb->getDbname());
    }

    public function testQueryBuilderForEach()
    {
        $this->initFixtures();

        $users = User::where('group_id = 1');
        foreach ($users as $user) {
            $this->assertEquals(1, $user['group_id']);
        }
    }

    public function testInsertWithSqlObject()
    {
        $this->initFixtures();

        $this->db->insert('users', array(
            'group_id' => '1',
            'name' => (object) '1 + 1',
            'address' => 'test',
        ));

        $id = $this->db->lastInsertId('prefix_member_id_seq');
        $user = $this->db->select('users', $id);

        $this->assertNotEquals('1 + 1', $user['name']);
        $this->assertEquals('2', $user['name']);
    }

    public function testUpdateWithSqlObject()
    {
        $this->initFixtures();

        $this->db->update('users', array('group_id' => (object) 'group_id + 1'), array('id' => (object) '0.5 + 0.5'));

        $user = $this->db->select('users', 1);

        $this->assertEquals('2', $user['group_id']);
    }

    public function testDeleteWithSqlObject()
    {
        $this->initFixtures();

        $result = $this->db->delete('users', array('id' => (object) '0.5 + 0.5'));

        $this->assertEquals(1, $result);
        $this->assertFalse($this->db->select('users', 1));
    }

    public function testRecordWithSqlObject()
    {
        $this->initFixtures();

        $user = $this->db->find('users', 1);
        $groupId = $user['group_id'];

        $user['group_id'] = (object) 'group_id + 1';
        $user->save();
        $user->reload();

        $this->assertEquals($groupId + 1, $user['group_id']);
    }

    public function testGetTableFieldsButTableNotExists()
    {
        $this->setExpectedException('PDOException');
        $this->db->getTableFields('notExists');
    }

    public function testNewRecord()
    {
        $this->initFixtures();

        // Use record as array
        $user = User::where('id = 1');
        $this->assertEquals('1', $user['id']);

        // Use record as 2d array
        $users = User::where('group_id = 1');
        foreach ($users as $user) {
            $this->assertEquals(1, $user['group_id']);
        }

        $user1 = $this->db('users');
        $user2 = $this->db('users');
        $this->assertEquals($user1, $user2);
        $this->assertNotSame($user1, $user2);
    }

    public function testCreateRecord()
    {
        $this->initFixtures();

        $user = $this->db('users');

        $data = $user->toArray();
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('group_id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('address', $data);

        $user->fromArray(array(
            'group_id' => 1,
            'name' => 'John',
            'address' => 'xx street',
        ));
        $result = $user->save();

        $this->assertSame($result, $user);
    }

    public function testBeforeAndAfterCreateCallbacks()
    {
        $this->initFixtures();

        $this->db->setOption('recordNamespace', 'WeiTest\Db');

        $user = User::fromArray(array(
            'group_id' => 1,
            'name' => 'twin',
            'address' => 'xx street',
        ));

        $user->save();

        $this->assertEquals('beforeSave->beforeCreate->afterCreate->afterSave', $user->getEventResult());
    }

    public function testBeforeAndAfterDestroyCallbacks()
    {
        $this->initFixtures();

        $this->db->setOption('recordNamespace', 'WeiTest\Db');

        $user = $this->db->find('users', 1);

        $user->destroy();

        $this->assertEquals('beforeDestroy->afterDestroy', $user->getEventResult());
    }

    public function testCreateCollection()
    {
        $this->initFixtures();

        $users = $this->db('users');

        $users->fromArray(array(
            array(
                'group_id' => 1,
                'name' => 'John',
                'address' => 'xx street',
            ),
            array(
                'group_id' => 2,
                'name' => 'Tome',
                'address' => 'xx street',
            ),
        ));
    }

    public function testFindRecordAndDestroy()
    {
        $this->initFixtures();

        $user = User::find(array('id' => 1));
        $result = $user->destroy();

        $this->assertInstanceOf('\Wei\Record', $result);

        $user = User::find(array('id' => 1));
        $this->assertFalse($user);
    }

    public function testDeleteRecordByQueryBuilder()
    {
        $this->initFixtures();

        $result = User::where('group_id = ?', 1)->delete();
        $this->assertEquals(2, $result);

        $result = User::delete(array('group_id' => 1));
        $this->assertEquals(0, $result);
    }

    public function testFindCollectionAndDestroy()
    {
        $this->initFixtures();

        $users = User::where('group_id = 1');
        $users->destroy();

        $users = User::where('group_id = 1');
        $this->assertEquals(0, count($users));
    }

    public function testFindRecordAndUpdate()
    {
        $this->initFixtures();

        $user = User::find(array('id' => 1));
        $user['name'] = 'William';
        $result = $user->save();
        $this->assertSame($result, $user);

        $user = User::find(array('id' => 1));
        $this->assertEquals('William', $user['name']);
    }

    public function testFindCollectionAndUpdate()
    {
        $this->initFixtures();

        $users = User::where('group_id = 1');
        $number = $users->length();
        $this->assertEquals(2, $number);

        foreach ($users as $user) {
            $user['group_id'] = 2;
        }
        $users->save();

        $users = User::where('group_id = 2');
        $this->assertEquals(2, $users->length());
    }

    public function testCreateCollectionAndSave()
    {
        $this->initFixtures();

        // Creates a member collection
        $users = $this->db('users');

        $john = User::fromArray(array(
            'group_id' => 2,
            'name' => 'John',
            'address' => 'xx street',
        ));

        $larry = User::fromArray(array(
            'group_id' => 3,
            'name' => 'Larry',
            'address' => 'xx street',
        ));

        // Adds record to collection
        $users->fromArray(array(
            $john,
        ));

        // Or adds by [] operator
        $users[] = $larry;

        /** @var $users \Wei\Record */
        $result = $users->save();

        $this->assertSame($result, $users);

        // Find out member by id
        $users = User::indexBy('id')->where(array('id' => array($john['id'], $larry['id'])));

        $this->assertEquals('John', $users[$john['id']]['name']);
        $this->assertEquals('Larry', $users[$larry['id']]['name']);
    }

    public function testDestroyRecordAndFindAgainReturnFalse()
    {
        $this->initFixtures();

        $user = $this->db('users');
        $result = $user->find(array('id' => 1))->destroy();

        $this->assertInstanceOf('\Wei\Record', $result);

        $user = User::find(array('id' => 1));
        $this->assertFalse($user);
    }

    public function testSaveOnNoFiledChanged()
    {
        $this->initFixtures();
        $record = $this->db->init('users', array('id' => 1), false);
        $record = $record->save();

        $this->assertInstanceOf('\Wei\Record', $record);
    }

    public function testPrimaryKey()
    {
        $this->initFixtures();

        $record = $this->db->init('users');
        $this->assertEquals('id', $record->getPrimaryKey());

        $record->setPrimaryKey('testId');
        $this->assertEquals('testId', $record->getPrimaryKey());
    }

    public function testIsNew()
    {
        $this->initFixtures();

        $record = $this->db->init('users', array('id' => 1), true);
        $this->assertTrue($record->isNew());

        $record = $this->db->init('users', array('id' => 1), false);
        $this->assertFalse($record->isNew());
    }

    public function testFindByPrimaryKey()
    {
        $this->initFixtures();

        $record = User::find(1);
        $this->assertEquals(1, $record['id']);

        $record = User::find('1');
        $this->assertEquals(1, $record['id']);
    }

    public function testInvalidLimit()
    {
        $this->initFixtures();
        $user = $this->db('users');

        $user->limit(-1);
        $this->assertEquals(1, $user->getSqlPart('limit'));

        $user->limit(0);
        $this->assertEquals(1, $user->getSqlPart('limit'));

        $user->limit('string');
        $this->assertEquals(1, $user->getSqlPart('limit'));
    }

    public function testInvalidOffset()
    {
        $this->initFixtures();
        $user = $this->db('users');

        $user->offset(-1);
        $this->assertEquals(0, $user->getSqlPart('offset'));

        $user->offset(-1.1);
        $this->assertEquals(0, $user->getSqlPart('offset'));

        $user->offset('string');
        $this->assertEquals(0, $user->getSqlPart('offset'));

        $user->offset(9848519079999155811);
        $this->assertEquals(0, $user->getSqlPart('offset'));
    }

    public function testInvalidPage()
    {
        $this->initFixtures();
        $user = $this->db('users');

        // @link http://php.net/manual/en/language.types.integer.php#language.types.integer.casting.from-float
        // (984851907999915581 - 1) * 10
        // => 9.8485190799992E+18
        // => (int)9.8485190799992E+18
        // => -8598224993710352384
        // => 0
        $user->page(984851907999915581);
        $this->assertEquals(0, $user->getSqlPart('offset'));
    }

    public function testMax()
    {
        $this->initFixtures();

        $result = $this->db->max('users', 'id');
        $this->assertInternalType('float', $result);
        $this->assertEquals(2, $result);
    }

    public function testMin()
    {
        $this->initFixtures();

        $result = $this->db->min('users', 'id');
        $this->assertInternalType('float', $result);
        $this->assertEquals(1, $result);
    }

    public function testAvg()
    {
        $this->initFixtures();

        $result = $this->db->avg('users', 'id');
        $this->assertInternalType('float', $result);
        $this->assertEquals(1.5, $result);
    }

    public function testSaveDestroyRecord()
    {
        $this->initFixtures();

        $user = $this->db->find('users', 1);
        $user->destroy();

        $user->save();

        $user = $this->db->find('users', 1);
        $this->assertFalse($user);
    }

    public function testSaveWithNullPrimaryKey()
    {
        $this->initFixtures();

        $user = $this->db('users');
        $user->save(array(
            'id' => null,
            'group_id' => '1',
            'name' => 'twin',
            'address' => 'test',
        ));

        $this->assertNotNull($user['id']);

        $user = $this->db('users');
        $user->save(array(
            'id' => '',
            'group_id' => '1',
            'name' => 'twin',
            'address' => 'test',
        ));

        $this->assertNotNull($user['id']);
    }

    public function testNullAsCollectionKey()
    {
        $this->initFixtures();

        $users = $this->db('users');

        $users[] = $this->db('users');
        $users[] = $this->db('users');
        $users[] = $this->db('users');
        $users[] = $this->db('users');

        $this->assertEquals(4, $users->length());
    }

    public function testSetDataWithProperty()
    {
        $this->initFixtures();

        $user = $this->db('users');

        $user['table'] = 234;

        $this->assertNotEquals(234, $user->getTable());
        $this->assertEquals('users', $user->getTable());
    }

    public function testAddNotRecordToCollection()
    {
        $this->initFixtures();

        $users = $this->db('users');
        $user = $this->db('users');

        // Make sure $users is a collection
        $users[] = $user;

        $this->setExpectedException('InvalidArgumentException',
            'Value for collection must be an instance of Wei\Record');

        // Assign non record value to raise an exception
        $users[] = 234;
    }

    public function testGetPdo()
    {
        $this->assertInstanceOf('PDO', $this->db->getPdo());
    }

    public function testIncrAndDecr()
    {
        $this->initFixtures();

        $user = User::find(1);

        $groupId = $user['group_id'];

        $user->incr('group_id', 2);
        $user->save();
        $user->reload();

        $this->assertEquals($groupId + 2, $user['group_id']);

        $user->decr('group_id');
        $user->save();
        $user->reload();

        $this->assertEquals($groupId + 2 - 1, $user['group_id']);
    }

    public function testCreateOrUpdate()
    {
        $this->initFixtures();

        $id = null;
        $user = User::findOrInit($id, array(
            'group_id' => 2,
            'name' => 'twin',
            'address' => 'xx street',
        ));

        $this->assertTrue($user->isNew());
        $this->assertEquals(2, $user['group_id']);

        $user = User::findOrInit(1, array(
            'group_id' => 2,
            'name' => 'twin',
            'address' => 'xx street',
        ));

        $this->assertFalse($user->isNew());
    }

    public function testDetach()
    {
        $this->initFixtures();

        $user = User::find(1);

        $this->assertFalse($user->isDetached());

        $user->detach();

        $this->assertTrue($user->isDetached());

        $user->save();

        $this->assertTrue($user->isDestroyed());

        $newMember = User::find(1);

        $this->assertNull($newMember);
    }

    public function testRecordFetchColumn()
    {
        $this->initFixtures();

        $count = User::select('COUNT(id)')->fetchColumn();
        $this->assertEquals(2, $count);

        $count = User::select('COUNT(id)')->fetchColumn(array('id' => 1));
        $this->assertEquals(1, $count);
    }

    public function testFillable()
    {
        $this->initFixtures();

        /** @var $user \Wei\Record */
        $user = $this->db('users');

        $user->setOption('fillable', array('name'));
        $this->assertEquals(true, $user->isFillable('name'));

        $user->fromArray(array(
            'id' => '1',
            'name' => 'name',
        ));

        $this->assertNull($user['id']);
        $this->assertEquals('name', $user['name']);
    }

    public function testGuarded()
    {
        $this->initFixtures();

        /** @var $user \Wei\Record */
        $user = $this->db('users');

        $user->setOption('guarded', array('id', 'name'));

        $this->assertEquals(false, $user->isFillable('id'));
        $this->assertEquals(false, $user->isFillable('name'));

        $user->fromArray(array(
            'id' => '1',
            'group_id' => '2',
            'name' => 'name',
        ));

        $this->assertNull($user['id']);
        $this->assertEquals('2', $user['group_id']);
        $this->assertNull($user['name']);
    }

    public function testCache()
    {
        $this->initFixtures();

        $user = $this->getMemberFromCache(1);
        $this->assertEquals('twin', $user['name']);

        $user->save(array(
            'name' => 'twin2',
        ));

        $user = $this->getMemberFromCache(1);
        $this->assertEquals('twin', $user['name']);

        $user->clearTagCache();

        $user = $this->getMemberFromCache(1);
        $this->assertEquals('twin2', $user['name']);

        wei()->cache->clear();
    }

    public function testCacheWithJoin()
    {
        $this->initFixtures();

        $user = $this->db('users')
            ->select('prefix_member.*')
            ->leftJoin('prefix_member_group', 'prefix_member.group_id = prefix_member_group.id')
            ->where('prefix_member.id = 1')
            ->tags()
            ->cache();

        // Fetch from db
        $data = $user->fetch();
        $this->assertEquals('twin', $data['name']);

        User::where('id = 1')->update("name = 'twin2'");

        // Fetch from cache
        $data = $user->fetch();
        $this->assertEquals('twin', $data['name']);

        // Clear cache
        wei()->tagCache('prefix_member')->clear();
        wei()->tagCache('prefix_member', 'prefix_member_group')->reload();

        // Fetch from db
        $data = $user->fetch();
        $this->assertEquals('twin2', $data['name']);
    }

    public function testCustomCacheTags()
    {
        $this->initFixtures();

        $user = $this->db('users')
            ->select('prefix_member.*')
            ->leftJoin('prefix_member_group', 'prefix_member.group_id = prefix_member_group.id')
            ->where('prefix_member.id = 1')
            ->tags(array('users', 'member_group'))
            ->cache();

        // Fetch from db
        $data = $user->fetch();
        $this->assertEquals('twin', $data['name']);

        User::where('id = 1')->update("name = 'twin2'");

        // Fetch from cache
        $data = $user->fetch();
        $this->assertEquals('twin', $data['name']);

        // Clear cache
        wei()->tagCache('users')->clear();
        wei()->tagCache('users', 'member_group')->reload();

        // Fetch from db
        $data = $user->fetch();
        $this->assertEquals('twin2', $data['name']);

        wei()->cache->clear();
    }

    public function testCustomCacheKey()
    {
        $this->initFixtures();

        $user = User::cache()->setCacheKey('member-1')->tags(false)->find(array('id' => 1));

        $this->assertEquals(1, $user['id']);

        $cacheData = wei()->cache->get('member-1');
        $this->assertEquals('1', $cacheData[0]['id']);

        wei()->cache->clear();
    }

    protected function getMemberFromCache($id)
    {
        return User::cache(600)->find($id);
    }

    public function testUpdateWithParam()
    {
        $this->initFixtures();

        $row = User::update(array('address' => 'test address'));
        $this->assertEquals(2, $row);

        $user = User::find();
        $this->assertEquals('test address', $user['address']);

        // Update with where clause
        $row = User::where(array('name' => 'twin'))->update(array('address' => 'test address 2'));
        $this->assertEquals(1, $row);

        $user = User::findOne(array('name' => 'twin'));
        $this->assertEquals('test address 2', $user['address']);

        // Update with two where clauses
        $row = $this->db('users')
            ->where(array('name' => 'twin'))
            ->andWhere(array('group_id' => 1))
            ->update(array('address' => 'test address 3'));
        $this->assertEquals(1, $row);

        $user = User::findOne(array('name' => 'twin'));
        $this->assertEquals('test address 3', $user['address']);
    }

    public function testEmptyFrom()
    {
        $sql = User::resetSqlPart('from')->getSql();
        $this->assertEquals('SELECT * FROM member', $sql);

        $sql = User::from('member m')->getSql();
        $this->assertEquals('SELECT * FROM member m', $sql);
    }
}
