<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Ret;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\DbTrait;
use MiaoxingTest\Plugin\Model\Fixture\TestUser;
use Wei\Req;
use Wei\Res;

/**
 * @mixin \DbMixin
 *
 * @internal
 */
final class ModelTest extends BaseTestCase
{
    use DbTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::setTablePrefix('p_');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::dropTables();
        static::resetTablePrefix();
    }

    public function testSetter()
    {
        $this->initFixtures();

        $user = TestUser::find(1);

        $this->assertEquals('1', $user->id);

        $user->id = 2;

        $this->assertEquals('2', $user->id);
    }

    public function testFind()
    {
        $this->initFixtures();

        $user = TestUser::find(1);

        $this->assertSame('SELECT * FROM `p_test_users` WHERE `id` = 1 LIMIT 1', $user->getRawSql());
        $this->assertEquals('1', $user->id);
    }

    public function testFindWithArray()
    {
        $this->initFixtures();

        $user = TestUser::find([1, 2]);

        $this->assertSame('SELECT * FROM `p_test_users` WHERE `id` IN (1, 2) LIMIT 1', $user->getRawSql());
        $this->assertEquals('1', $user->id);
    }

    public function testFindOrFailFound()
    {
        $this->initFixtures();

        $user = TestUser::findOrFail(1);

        $this->assertInstanceOf(TestUser::class, $user);
    }

    public function testFindOrFailNotFound()
    {
        $this->initFixtures();

        $this->expectExceptionObject(new \Exception('Record not found', 404));

        TestUser::findOrFail(99);
    }

    public function testFindNotExistReturnsNull()
    {
        $this->initFixtures();

        $user = TestUser::find('not-exists');

        $this->assertSame('SELECT * FROM `p_test_users` WHERE `id` = ? LIMIT 1', $this->db->getLastQuery());
        $this->assertNull($user);
    }

    public function testFindNull()
    {
        $this->initFixtures();

        $user = TestUser::find(null);

        $this->assertNull($user);
    }

    public function testFindOrInitAndStatusIsNew()
    {
        $this->initFixtures();

        $user = TestUser::findOrInit(3, [
            'name' => 'name',
        ]);

        $this->assertSame('SELECT * FROM `p_test_users` WHERE `id` = 3 LIMIT 1', $user->getRawSql());
        $this->assertTrue($user->isNew());
        $this->assertFalse($user->isDestroyed());
    }

    public function testFindOrInitWithSameFields()
    {
        $this->initFixtures();

        // The init data may from request, contains key like id, name
        $user = TestUser::findOrInitBy(['id' => 3], ['name' => 'name', 'id' => '5']);

        $this->assertSame('SELECT * FROM `p_test_users` WHERE `id` = 3 LIMIT 1', $user->getRawSql());
        $this->assertSame(3, $user->id);
        $this->assertEquals('name', $user->name);
    }

    public function testFindOrCreate()
    {
        $this->initFixtures();

        $user = TestUser::findOrCreate(1, ['name' => 'test']);
        $this->assertSame(1, $user->id);
        $this->assertSame('twin', $user->name);

        $user = TestUser::findOrCreate(7, ['name' => 'test']);
        $this->assertSame(7, $user->id);
        $this->assertSame('test', $user->name);
    }

    public function testFindAll()
    {
        $this->initFixtures();

        $users = TestUser::findAll([1, 2]);

        $this->assertSame('SELECT * FROM `p_test_users` WHERE `id` IN (1, 2)', $users->getRawSql());
        $this->assertCount(2, $users);
        $this->assertEquals(1, $users[0]->id);
    }

    public function testFindBy()
    {
        $this->initFixtures();

        $user = TestUser::findBy('name', 'twin');

        $this->assertSame("SELECT * FROM `p_test_users` WHERE `name` = 'twin' LIMIT 1", $user->getRawSql());
        $this->assertEquals(1, $user->id);
    }

    public function testFindByOperator()
    {
        $this->initFixtures();

        $user = TestUser::findBy('id', '>', 1);

        $this->assertSame('SELECT * FROM `p_test_users` WHERE `id` > 1 LIMIT 1', $user->getRawSql());
        $this->assertEquals(2, $user->id);
    }

    public function testFindAllBy()
    {
        $this->initFixtures();

        $users = TestUser::findAllBy('id', '>', 1);

        $this->assertSame('SELECT * FROM `p_test_users` WHERE `id` > 1', $users->getRawSql());
        $this->assertSame(2, $users[0]->id);
        $this->assertSame('test', $users[0]->name);
    }

    public function testFindOrInitBy()
    {
        $this->initFixtures();

        // The init data may from request, contains key like id, name
        $user = TestUser::findOrInitBy(['id' => 3, 'name' => 'tom'], ['name' => 'name', 'id' => '5']);

        $this->assertSame("SELECT * FROM `p_test_users` WHERE `id` = 3 AND `name` = 'tom' LIMIT 1", $user->getRawSql());
        $this->assertSame(3, $user->id);
        $this->assertSame('name', $user->name);
    }

    public function testFindByOrFail()
    {
        $this->initFixtures();

        $this->expectExceptionObject(new \Exception('Record not found', 404));

        TestUser::findByOrFail('name', 'not-exists');
    }

    public function testFindFromReq()
    {
        $this->initFixtures();

        // POST users
        $req = new Req([
            'wei' => $this->wei,
            'fromGlobal' => false,
            'data' => ['id' => 1], // ignored
            'servers' => ['REQUEST_METHOD' => 'POST'],
        ]);
        $user = TestUser::findFromReq($req);
        $this->assertTrue($user->isNew());
        $this->assertNull($user->id);

        // GET users/1
        $req = new Req([
            'wei' => $this->wei,
            'fromGlobal' => false,
            'data' => ['id' => 1],
            'servers' => ['REQUEST_METHOD' => 'GET'],
        ]);
        $user = TestUser::findFromReq($req);
        $this->assertFalse($user->isNew());
        $this->assertSame(1, $user->id);

        // PATCH users/1
        $req = new Req([
            'wei' => $this->wei,
            'fromGlobal' => false,
            'data' => ['id' => 1],
            'servers' => ['REQUEST_METHOD' => 'PATCH'],
        ]);
        $user = TestUser::findFromReq($req);
        $this->assertFalse($user->isNew());
        $this->assertSame(1, $user->id);
    }

    public function testFirst()
    {
        $this->initFixtures();

        $user = TestUser::first();

        $this->assertSame(1, $user->id);
    }

    public function testAll()
    {
        $this->initFixtures();

        $users = TestUser::all();

        $this->assertCount(2, $users);
    }

    public function testIndexByAndAll()
    {
        $this->initFixtures();

        $users = TestUser::indexBy('name')->all();

        $this->assertArrayHasKey('twin', $users);
        $this->assertArrayHasKey('test', $users);

        $this->assertInstanceOf(TestUser::class, $users['twin']);
        $this->assertInstanceOf(TestUser::class, $users['test']);

        $users = $users->toArray();

        $this->assertArrayHasKey('twin', $users);
        $this->assertArrayHasKey('test', $users);
    }

    public function testIndexByMultipleTimes()
    {
        $this->initFixtures();

        $users = TestUser::indexBy('id')->all();

        $this->assertArrayHasKey(1, $users);

        $users->indexBy('name');
        $this->assertArrayHasKey('twin', $users);

        $users->indexBy('id');
        $this->assertArrayHasKey(1, $users);
    }

    public function testFixUndefinedOffset0WhenFetchEmptyData()
    {
        $this->initFixtures();

        $emptyMembers = TestUser::where(['group_id' => '3'])->indexBy('id')->fetchAll();
        $this->assertEmpty($emptyMembers);
    }

    public function testRealTimeIndexBy()
    {
        $this->initFixtures();

        $users = TestUser::all();

        $users = $users->indexBy('name')->toArray();

        $this->assertArrayHasKey('twin', $users);
        $this->assertArrayHasKey('test', $users);
    }

    public function testModelSave()
    {
        $this->initFixtures();

        // Existing member
        $user = TestUser::find(1);
        $user->address = 'address';
        $result = $user->save();

        $this->assertSame($result, $user);
        $this->assertEquals('1', $user->id);

        // New member save with data
        $user = TestUser::new();
        $this->assertTrue($user->isNew());
        $user->fromArray([
            'group_id' => '1',
            'name' => 'save',
            'address' => 'save',
        ]);
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

        $user = TestUser::new();

        $this->assertFalse($user->isLoaded());

        $user->find(1);

        $this->assertTrue($user->isLoaded());
    }

    public function testToArray()
    {
        $this->initFixtures();

        $user = TestUser::find(1)->toArray();

        $this->assertIsArray($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('group_id', $user);
        $this->assertArrayHasKey('name', $user);
        $this->assertArrayHasKey('address', $user);

        $user = TestUser::find(1)->toArray(['id', 'group_id']);
        $this->assertIsArray($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('group_id', $user);
        $this->assertArrayNotHasKey('name', $user);
        $this->assertArrayNotHasKey('address', $user);

        $user = TestUser::find(1)->toArray(['id', 'group_id']);
        $this->assertIsArray($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('group_id', $user);
        $this->assertArrayNotHasKey('name', $user);
        $this->assertArrayNotHasKey('address', $user);

        $user = TestUser::new()->toArray();
        $this->assertIsArray($user);
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('group_id', $user);
        $this->assertArrayHasKey('name', $user);
        $this->assertArrayHasKey('address', $user);
        $this->assertNull($user['id']);
        $this->assertSame(0, $user['group_id']); // default value
        $this->assertNull($user['name']);
        $this->assertSame('default address', $user['address']); // getAddressAttribute

        $users = TestUser::all()->toArray(['id', 'group_id']);
        $this->assertIsArray($users);
        $this->assertArrayHasKey(0, $users);
        $this->assertArrayHasKey('id', $users[0]);
        $this->assertArrayHasKey('group_id', $users[0]);
        $this->assertArrayNotHasKey('name', $users[0]);
    }

    public function testToArrayWithInvalidColumn()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: notExistColumn'));

        TestUser::new()->toArray(['notExistColumn']);
    }

    public function testToArrayWithPrepend()
    {
        $this->initFixtures();

        $users = TestUser::limit(1)->all();

        $data = $users->toArray(function (TestUser $user) {
            return [
                'newId' => $user->id + 1,
            ];
        });
        $this->assertSame(2, $data[0]['newId']);
        $this->assertArrayHasKey('id', $data[0]);

        $data = $users->toArray(['name'], function ($user) {
            return [
                'newId' => $user->id + 1,
            ];
        });
        $this->assertSame(2, $data[0]['newId']);
        $this->assertArrayNotHasKey('id', $data[0]);
    }

    public function testNewModelToArrayWithoutReturnFields()
    {
        $this->initFixtures();

        $user = TestUser::findOrInitBy(['id' => 9999]);

        $this->assertTrue($user->isNew());

        $data = $user->toArray();

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('group_id', $data);
        $this->assertArrayHasKey('name', $data);
    }

    public function testNewModelToArrayWithReturnFields()
    {
        $this->initFixtures();

        $user = TestUser::findOrInitBy(['id' => 9999]);

        $this->assertTrue($user->isNew());

        $data = $user->toArray(['group_id', 'name']);

        $this->assertArrayNotHasKey('id', $data);
        $this->assertArrayHasKey('group_id', $data);
        $this->assertArrayHasKey('name', $data);
    }

    public function testToJson()
    {
        $this->initFixtures();

        $user = TestUser::new();
        $this->assertJson($user->toJson());
    }

    public function testDestroy()
    {
        $this->initFixtures();

        $user = TestUser::find(1);

        $result = $user->destroy();

        $this->assertInstanceOf(TestUser::class, $result);

        $user = TestUser::find(1);

        $this->assertNull($user);
    }

    public function testDestroyById()
    {
        $this->initFixtures();

        $user = TestUser::destroy(2);
        $this->assertInstanceOf(TestUser::class, $user);

        $this->assertNull(TestUser::find(2));
    }

    public function testGetTable()
    {
        $this->initFixtures();

        $user = TestUser::find('1');

        $this->assertEquals('test_users', $user->getTable());
    }

    public function testColumnNotFound()
    {
        $this->initFixtures();

        $user = TestUser::find('1');

        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: notFound'));

        // @phpstan-ignore-next-line
        $user['notFound'];
    }

    public function testColl()
    {
        $this->initFixtures();

        $users = TestUser::all();

        $this->assertInstanceOf(TestUser::class, $users);

        $userArray = $users->toArray();
        $this->assertIsArray($userArray);
        foreach ($userArray as $user) {
            $this->assertIsArray($user);
        }
    }

    public function testFilter()
    {
        $this->initFixtures();

        $users = TestUser::all();

        $oneUsers = $users->filter(static function (TestUser $user) {
            return 1 === $user->id;
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

        $user = TestUser::find(1);

        $this->assertEquals('twin', $user['name']);
        $this->assertEquals('1', $user['group_id']);

        unset($user['name'], $user['group_id']);

        $this->assertNull($user['name']);
        $this->assertNull($user['group_id']);
    }

    public function testCount()
    {
        $users = TestUser::limit(1)->all();

        $this->assertCount(1, $users);
    }

    public function testReload()
    {
        $this->initFixtures();

        $user = TestUser::find(1);
        $user2 = TestUser::find(1);

        $user->group_id = 2;
        $user->save();

        $this->assertNotEquals($user->group_id, $user2->group_id);
        $this->assertEquals(1, $user->getLoadTimes());

        $user2->reload();
        $this->assertEquals($user->group_id, $user2->group_id);
        $this->assertEquals(2, $user2->getLoadTimes());
    }

    public function testChunk()
    {
        $this->initFixtures();

        $this->db->batchInsert('test_users', [
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

        $user = TestUser::new();

        $count = 0;
        $times = 0;
        $result = $user->chunk(2, function (TestUser $users, $page) use (&$count, &$times) {
            $count += count($users);
            ++$times;
        });

        $this->assertEquals(4, $count);
        $this->assertEquals(2, $times);
        $this->assertTrue($result);
    }

    public function testChunkBreak()
    {
        $this->initFixtures();

        $user = TestUser::new();

        $count = 0;
        $times = 0;
        $result = $user->chunk(1, function (TestUser $users, $page) use (&$count, &$times) {
            $count += count($users);
            ++$times;
            return false;
        });

        $this->assertEquals(1, $count);
        $this->assertEquals(1, $times);
        $this->assertFalse($result);
    }

    public function testIsChanged()
    {
        $this->initFixtures();

        $user = TestUser::new();
        $this->assertFalse($user->isChanged());

        $user->name = 'tt';
        $user->group_id = 1;
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

        $users = TestUser::where('group_id', 1)->all();
        foreach ($users as $user) {
            $this->assertEquals(1, $user['group_id']);
        }
    }

    public function testSaveRawObject()
    {
        $this->initFixtures();

        $user = TestUser::find(1);
        $group_id = $user->group_id;

        $user->group_id = (object) 'group_id + 1';
        $user->save();
        $user->reload();

        $this->assertSame($group_id + 1, $user->group_id);
    }

    public function testNewRecord()
    {
        $this->initFixtures();

        // Use record as array
        $user = TestUser::find(1);
        $this->assertEquals('1', $user['id']);

        // Use record as 2d array
        $users = TestUser::where('group_id', 1)->all();
        foreach ($users as $user) {
            $this->assertEquals(1, $user['group_id']);
        }

        $user1 = TestUser::new();
        $user2 = TestUser::new();
        $this->assertEquals($user1, $user2);
        $this->assertNotSame($user1, $user2);
    }

    public function testSaveReturnThis()
    {
        $this->initFixtures();

        $user = TestUser::fromArray([
            'group_id' => 1,
            'name' => 'John',
            'address' => 'xx street',
        ]);
        $result = $user->save();

        $this->assertSame($result, $user);
    }

    public function testBeforeAndAfterCreateCallbacks()
    {
        $this->initFixtures();

        $user = TestUser::fromArray([
            'group_id' => 1,
            'name' => 'twin',
            'address' => 'xx street',
        ]);

        $user->save();

        $this->assertEquals('beforeSave->beforeCreate->afterCreate->afterSave', $user->getEventResult());
    }

    public function testBeforeAndAfterDestroyCallbacks()
    {
        $this->initFixtures();

        $user = TestUser::find(1);

        $user->destroy();

        $this->assertEquals('beforeDestroy->afterDestroy', $user->getEventResult());
    }

    public function testFromArray()
    {
        $this->initFixtures();

        $user = TestUser::new();

        $user->fromArray([
            'group_id' => 2,
            'ignored' => true,
        ]);

        $array = $user->toArray();
        $this->assertSame(2, $array['group_id']);
        $this->assertArrayNotHasKey('ignored', $array);
    }

    public function testFromArrayMultipleLevelWontBecomeColl()
    {
        $this->initFixtures();

        $users = TestUser::new();

        $users->fromArray([
            [
                'group_id' => 1,
                'name' => 'John',
                'address' => 'xx street',
            ],
            [
                'group_id' => 2,
                'name' => 'Tome',
                'address' => 'xx street',
            ],
        ]);

        $this->assertFalse($users->isColl());
    }

    public function testFromArrayWillIgnoreRelation()
    {
        $this->initFixtures();

        $user = TestUser::new();

        $user->fromArray([
            'group' => [
                'id' => 1,
                'name' => 'test',
            ],
        ]);

        $array = $user->toArray();
        $this->assertArrayNotHasKey('group', $array);

        $this->assertNull($user->group);

        $array = $user->toArray();
        $this->assertNull($array['group']);
    }

    public function testFindCollectionAndDestroy()
    {
        $this->initFixtures();

        $users = TestUser::findAllBy('group_id', 1);
        $users->destroy();

        $users = TestUser::findAllBy('group_id', 1);
        $this->assertCount(0, $users);
    }

    public function testFindAndUpdate()
    {
        $this->initFixtures();

        $user = TestUser::find(1);
        $user->name = 'William';
        $result = $user->save();
        $this->assertSame($result, $user);

        $user = TestUser::find(1);
        $this->assertEquals('William', $user->name);
    }

    public function testFindCollectionAndUpdate()
    {
        $this->initFixtures();

        $users = TestUser::findAllBy('group_id', 1);

        $this->assertCount(2, $users);

        foreach ($users as $user) {
            $user->group_id = 2;
        }
        $users->save();

        $users = TestUser::findAllBy('group_id', 2);
        $this->assertCount(2, $users);
    }

    public function testCreateCollectionAndSave()
    {
        $this->initFixtures();

        // Creates a user collection
        $users = TestUser::newColl();

        $john = TestUser::fromArray([
            'group_id' => 2,
            'name' => 'John',
            'address' => 'xx street',
        ]);

        $larry = TestUser::fromArray([
            'group_id' => 3,
            'name' => 'Larry',
            'address' => 'xx street',
        ]);

        // Adds record to collection
        $users->fromArray([
            $john,
        ]);

        // Or adds by [] operator
        $users[] = $larry;

        $result = $users->save();

        $this->assertSame($result, $users);

        // Find out member by id
        $users = TestUser::indexBy('id')->whereIn('id', [$john['id'], $larry['id']])->all();

        $this->assertEquals('John', $users[$john['id']]['name']);
        $this->assertEquals('Larry', $users[$larry['id']]['name']);
    }

    public function testSaveOnNoAttributeChanged()
    {
        $this->initFixtures();

        $user = TestUser::new();
        $result = $user->save();

        $this->assertInstanceOf(TestUser::class, $result);
    }

    public function testPrimaryKey()
    {
        $user = TestUser::new();
        $this->assertEquals('id', $user->getPrimaryKey());

        $user->setPrimaryKey('testId');
        $this->assertEquals('testId', $user->getPrimaryKey());
    }

    public function testIsNew()
    {
        $user = TestUser::new();

        $this->assertTrue($user->isNew());
    }

    public function testIsNewRemainTrueWhenFindOrInitNoRecord()
    {
        $user = TestUser::new();
        $user->findOrInit(99);

        $this->assertTrue($user->isNew());
    }

    public function testIsNewBecomeFalseAfterSave()
    {
        $this->initFixtures();

        $user = TestUser::save();

        $this->assertFalse($user->isNew());
    }

    public function testFindByPrimaryKey()
    {
        $this->initFixtures();

        $user = TestUser::find(1);
        $this->assertSame(1, $user->id);

        $user = TestUser::find('1');
        $this->assertSame(1, $user->id);
    }

    public function testSaveDestroyedModel()
    {
        $this->initFixtures();

        $user = TestUser::find(1);
        $user->destroy();

        $user->save();

        $user = TestUser::find(1);
        $this->assertNull($user);
    }

    public function testSaveWithNullPrimaryKey()
    {
        $this->initFixtures();

        $user = TestUser::new();
        $user->save([
            'id' => null,
            'group_id' => '1',
            'name' => 'twin',
            'address' => 'test',
        ]);

        $this->assertNotNull($user['id']);

        $user = TestUser::new();
        $user->save([
            'id' => '',
            'group_id' => '1',
            'name' => 'twin',
            'address' => 'test',
        ]);

        $this->assertNotNull($user['id']);
    }

    public function testNullAsCollectionKey()
    {
        $this->initFixtures();

        $users = TestUser::newColl();

        $users[] = TestUser::new();
        $users[] = TestUser::new();
        $users[] = TestUser::new();
        $users[] = TestUser::new();

        $this->assertCount(4, $users);
    }

    public function testSetInvalidPropertyName()
    {
        $this->initFixtures();

        $user = TestUser::new();

        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: abc'));

        // TODO 改为 table
        // @phpstan-ignore-next-line
        $user->abc = 234;
    }

    public function testAddNotModelToCollection()
    {
        $this->initFixtures();

        $users = TestUser::newColl();
        $user = TestUser::new();

        $this->expectExceptionObject(new \InvalidArgumentException(
            'Value for collection must be an instance of Wei\Record'
        ));

        // Assign non record value to raise an exception
        $users[] = 234;
    }

    public function testIncrAndDecr()
    {
        $this->initFixtures();

        $user = TestUser::find(1);

        $group_id = $user['group_id'];

        $user->incr('group_id', 2);
        $user->save();
        $user->reload();

        $this->assertEquals($group_id + 2, $user['group_id']);

        $user->decr('group_id');
        $user->save();
        $user->reload();

        $this->assertEquals($group_id + 2 - 1, $user['group_id']);
    }

    public function testCreateOrUpdate()
    {
        $this->initFixtures();

        $id = null;
        $user = TestUser::findOrInit($id, [
            'group_id' => 2,
            'name' => 'twin',
            'address' => 'xx street',
        ]);

        $this->assertTrue($user->isNew());
        $this->assertEquals(2, $user['group_id']);

        $user = TestUser::findOrInit(1, [
            'group_id' => 2,
            'name' => 'twin',
            'address' => 'xx street',
        ]);

        $this->assertFalse($user->isNew());
    }

    public function testDetach()
    {
        $this->initFixtures();

        $user = TestUser::find(1);

        $this->assertFalse($user->isDetached());

        $user->detach();

        $this->assertTrue($user->isDetached());

        $user->save();

        $this->assertTrue($user->isDestroyed());

        $newMember = TestUser::find(1);

        $this->assertNull($newMember);
    }

    public function testModelFetchColumn()
    {
        $this->initFixtures();

        $count = TestUser::selectRaw('COUNT(id)')->fetchColumn();
        $this->assertEquals(2, $count);

        $count = TestUser::selectRaw('COUNT(id)')->fetchColumn(['id' => 1]);
        $this->assertEquals(1, $count);
    }

    public function testFillable()
    {
        $this->initFixtures();

        $user = TestUser::new();

        $user->setFillable(['name']);
        $this->assertTrue($user->isFillable('name'));

        $user->fromArray([
            'id' => '1',
            'name' => 'name',
        ]);

        $this->assertNull($user->id);
        $this->assertEquals('name', $user->name);
    }

    public function testGuarded()
    {
        $this->initFixtures();

        $user = TestUser::new();

        $user->setGuarded(['id', 'name']);

        $this->assertFalse($user->isFillable('id'));
        $this->assertFalse($user->isFillable('name'));

        $user->fromArray([
            'id' => '1',
            'group_id' => '2',
            'name' => 'name',
        ]);

        $this->assertNull($user['id']);
        $this->assertEquals('2', $user['group_id']);
        $this->assertNull($user['name']);
    }

    public function testUpdateWithParam()
    {
        $this->initFixtures();

        $row = TestUser::update(['address' => 'test address']);
        $this->assertSame(2, $row);

        $user = TestUser::first();
        $this->assertSame('test address', $user['address']);

        // Update with where clause
        $row = TestUser::where(['name' => 'twin'])->update(['address' => 'test address 2']);
        $this->assertEquals(1, $row);

        $user = TestUser::findBy(['name' => 'twin']);
        $this->assertEquals('test address 2', $user['address']);

        // Update with two where clauses
        $row = TestUser::where(['name' => 'twin'])
            ->where(['group_id' => 1])
            ->update(['address' => 'test address 3']);
        $this->assertEquals(1, $row);

        $user = TestUser::findBy(['name' => 'twin']);
        $this->assertEquals('test address 3', $user['address']);
    }

    public function testWasRecentlyCreated()
    {
        $this->initFixtures();

        $user = TestUser::first();
        $this->assertFalse($user->wasRecentlyCreated());

        $user = TestUser::save();
        $this->assertTrue($user->wasRecentlyCreated());

        $user = TestUser::find($user->id);
        $this->assertFalse($user->wasRecentlyCreated());
    }

    public function testToRet()
    {
        $this->initFixtures();;

        $user = TestUser::first();

        $ret = $user->toRet();
        $this->assertInstanceOf(Ret::class, $ret);
        $this->assertSame($user->id, $ret['data']['id']);

        $this->assertSame($user, $ret->getMetadata('model'));

        $ret = $user->toRet(['data' => 'custom']);
        $this->assertSame('custom', $ret['data']);

        $this->assertSame($user, $ret->getMetadata('model'));
    }

    public function testToRetToRes()
    {
        $this->initFixtures();

        $res = new Res(['wei' => $this->wei]);

        $user = TestUser::first();
        $user->toRet()->toRes(null, $res);
        $this->assertSame(200, $res->getStatusCode());

        $user = TestUser::save();
        $user->toRet()->toRes(null, $res);
        $this->assertSame(201, $res->getStatusCode());
    }

    public function testFindByOrCreateWithNewAttribute()
    {
        $this->initFixtures();

        $user = TestUser::findByOrCreate(['name' => 'new']);
        $this->assertFalse($user->isNew());
        $this->assertSame(0, $user->group_id);
    }

    public function testFindByOrCreateWithNewData()
    {
        $this->initFixtures();

        $user = TestUser::findByOrCreate(['name' => 'new'], ['group_id' => 2]);
        $this->assertFalse($user->isNew());
        $this->assertSame(2, $user->group_id);
    }

    public function testFindByOrCreateWontSaveExistingData()
    {
        $this->initFixtures();

        $user = TestUser::findByOrCreate(['name' => 'twin'], ['group_id' => 2]);
        $this->assertSame(1, $user->group_id);
    }

    public function testIsset()
    {
        $user = TestUser::new();
        $this->assertTrue($user->id ?? true);
        $this->assertTrue($user->notFound ?? true);
    }
}
