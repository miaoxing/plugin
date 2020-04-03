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

        $this->assertCount(2, $users);
    }

    public function testIndexByAndAll()
    {
        $this->initFixtures();

        $users = User::indexBy('name')->all();

        $this->assertArrayHasKey('twin', $users);
        $this->assertArrayHasKey('test', $users);

        $this->assertInstanceOf(User::class, $users['twin']);
        $this->assertInstanceOf(User::class, $users['test']);

        $users = $users->toArray();

        $this->assertArrayHasKey('twin', $users);
        $this->assertArrayHasKey('test', $users);
    }

    public function testIndexByMultipleTimes()
    {
        $this->initFixtures();

        $users = User::indexBy('id')->all();

        $this->assertArrayHasKey(1, $users);

        $users->indexBy('name');
        $this->assertArrayHasKey('twin', $users);

        $users->indexBy('id');
        $this->assertArrayHasKey(1, $users);
    }

    public function testFixUndefinedOffset0WhenFetchEmptyData()
    {
        $this->initFixtures();

        $emptyMembers = User::where(['group_id' => '3'])->indexBy('id')->fetchAll();
        $this->assertEmpty($emptyMembers);
    }

    public function testRealTimeIndexBy()
    {
        $this->initFixtures();

        $users = User::all();

        $users = $users->indexBy('name')->toArray();

        $this->assertArrayHasKey('twin', $users);
        $this->assertArrayHasKey('test', $users);
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
        $user = User::new();
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

        $user = User::new();

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

    public function testToArray()
    {
        $this->initFixtures();

        $user = User::find(1)->toArray();

        $this->assertIsArray($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('groupId', $user);
        $this->assertArrayHasKey('name', $user);
        $this->assertArrayHasKey('address', $user);

        $user = User::find(1)->toArray(array('id', 'groupId'));
        $this->assertIsArray($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('groupId', $user);
        $this->assertArrayNotHasKey('name', $user);
        $this->assertArrayNotHasKey('address', $user);

        $user = User::find(1)->toArray(array('id', 'groupId'));
        $this->assertIsArray($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('groupId', $user);
        $this->assertArrayNotHasKey('name', $user);
        $this->assertArrayNotHasKey('address', $user);

        $user = User::new()->toArray();
        $this->assertIsArray($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('groupId', $user);
        $this->assertArrayHasKey('name', $user);
        $this->assertArrayHasKey('address', $user);
        $this->assertNull($user['id']);
        $this->assertSame(0, $user['groupId']); // default value
        $this->assertNull($user['name']);
        $this->assertSame('default address', $user['address']); // getAddressAttribute

        $users = User::all()->toArray(array('id', 'groupId'));
        $this->assertIsArray($users);
        $this->assertArrayHasKey(0, $users);
        $this->assertArrayHasKey('id', $users[0]);
        $this->assertArrayHasKey('groupId', $users[0]);
        $this->assertArrayNotHasKey('name', $users[0]);
    }

    public function testToArrayWithInvalidColumn()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: notExistColumn'));

        User::new()->toArray(['notExistColumn']);
    }

    public function testNewModelToArrayWithoutReturnFields()
    {
        $this->initFixtures();

        $user = User::findOrInitBy(array('id' => 9999));

        $this->assertTrue($user->isNew());

        $data = $user->toArray();

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('groupId', $data);
        $this->assertArrayHasKey('name', $data);
    }

    public function testNewModelToArrayWithReturnFields()
    {
        $this->initFixtures();

        $user = User::findOrInitBy(array('id' => 9999));

        $this->assertTrue($user->isNew());

        $data = $user->toArray(array('groupId', 'name'));

        $this->assertArrayNotHasKey('id', $data);
        $this->assertArrayHasKey('groupId', $data);
        $this->assertArrayHasKey('name', $data);
    }

    public function testToJson()
    {
        $this->initFixtures();

        $user = User::new();
        $this->assertJson($user->toJson());
    }

    public function testDestroy()
    {
        $this->initFixtures();

        $user = User::find(1);

        $result = $user->destroy();

        $this->assertInstanceOf(User::class, $result);

        $user = User::find(1);

        $this->assertNull($user);
    }

    public function testDestroyById()
    {
        $this->initFixtures();

        $user = User::destroy(2);
        $this->assertInstanceOf(User::class, $user);

        $this->assertNull(User::find(2));
    }

    public function testGetTable()
    {
        $this->initFixtures();

        $user = User::find('1');

        $this->assertEquals('users', $user->getTable());
    }

    public function testColumnNotFound()
    {
        $this->initFixtures();

        $user = User::find('1');

        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: notFound'));

        $user['notFound'];
    }

    public function testColl()
    {
        $this->initFixtures();

        $users = User::all();

        $this->assertInstanceOf(User::class, $users);

        $userArray = $users->toArray();
        $this->assertIsArray($userArray);
        foreach ($userArray as $user) {
            $this->assertIsArray($user);
        }
    }

    public function testFilter()
    {
        $this->initFixtures();

        $users = User::all();

        $oneUsers = $users->filter(static function (User $user) {
            return $user->id === 1;
        });

        $this->assertCount(1, $oneUsers);
        $this->assertEquals(1, $oneUsers[0]->id);

        $noMembers = $users->filter(static function () {
            return false;
        });

        $this->assertCount(0, $noMembers);
        $this->assertEmpty($noMembers->toArray());
    }

    public function testOffsetUnset()
    {
        $this->initFixtures();

        $user = User::find(1);

        $this->assertEquals('twin', $user['name']);
        $this->assertEquals('1', $user['groupId']);

        unset($user['name']);
        unset($user['groupId']);

        $this->assertEquals(null, $user['name']);
        $this->assertEquals(null, $user['groupId']);
    }

    public function testCount()
    {
        $users = User::limit(1)->all();

        $this->assertCount(1, $users);
    }

    public function testReload()
    {
        $this->initFixtures();

        $user = User::find(1);
        $user2 = User::find(1);

        $user->groupId = 2;
        $user->save();

        $this->assertNotEquals($user->groupId, $user2->groupId);
        $this->assertEquals(1, $user->getLoadTimes());

        $user2->reload();
        $this->assertEquals($user->groupId, $user2->groupId);
        $this->assertEquals(2, $user2->getLoadTimes());
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

    public function testGetAttribute()
    {
        $user = User::new();
        $this->assertEquals('default address', $user->address);
    }
}
