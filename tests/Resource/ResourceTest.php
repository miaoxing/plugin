<?php

namespace MiaoxingTest\Plugin\Resource;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\DbTrait;
use MiaoxingTest\Plugin\Model\Fixture\TestUser;

class ResourceTest extends BaseTestCase
{
    use DbTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::setTablePrefix('p_');
        static::dropTables();
        static::createTables();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::dropTables();
        static::resetTablePrefix();
    }

    public function testToArray()
    {
        $user = TestUser::new();
        $user->name = 'test';

        $resource = new class () extends TestUserResource {
            public function transform(TestUser $user)
            {
                return [
                    'name' => $user->name,
                ];
            }
        };

        $this->assertSame(['name' => 'test'], $resource->toArray($user)['data']);
    }

    public function testWhen()
    {
        $user = TestUser::new();

        $resource = new class () extends TestUserResource {
            public function transform(TestUser $user)
            {
                return [
                    'name' => $this->when(true, 'test'),
                ];
            }
        };

        $this->assertSame(['name' => 'test'], $resource->toArray($user)['data']);
    }

    public function testWhenFalse()
    {
        $user = TestUser::new();

        $resource = new class () extends TestUserResource {
            public function transform(TestUser $user)
            {
                return [
                    'name' => $this->when(false, 'test'),
                ];
            }
        };

        $this->assertSame([], $resource->toArray($user)['data']);
    }

    public function testMergeWhenTrue()
    {
        $user = TestUser::new();

        $resource = new class () extends TestUserResource {
            public function transform(TestUser $user)
            {
                return [
                    'id' => 1,
                    $this->mergeWhen(true, [
                        'name' => 'test',
                    ]),
                ];
            }
        };

        $this->assertSame(['id' => 1, 'name' => 'test'], $resource->toArray($user)['data']);
    }

    public function testMergeWhenFalse()
    {
        $user = TestUser::new();

        $resource = new class () extends TestUserResource {
            public function transform(TestUser $user)
            {
                return [
                    'id' => 1,
                    $this->mergeWhen(false, [
                        'name' => 'test',
                    ]),
                ];
            }
        };

        $this->assertSame(['id' => 1], $resource->toArray($user)['data']);
    }

    public function testExtract()
    {
        $user = TestUser::new();
        $user->name = 'foo';
        $user->address = 'bar';

        $resource = new class () extends TestUserResource {
            public function transform(TestUser $user)
            {
                return [
                    'id' => 1,
                    $this->extract($user, [
                        'name',
                        'address',
                    ]),
                ];
            }
        };

        $this->assertSame([
            'id' => 1,
            'name' => 'foo',
            'address' => 'bar',
        ], $resource->toArray($user)['data']);
    }

    public function testWhenLoaded()
    {
        $this->initFixtures();

        $user = TestUser::new();
        $user->find(1);

        $resource = new class () extends TestUserResource {
            public function transform(TestUser $user)
            {
                return [
                    'id' => 1,
                    'group' => TestUserGroupResource::whenLoaded($user, 'group'),
                ];
            }
        };

        $this->assertSame([
            'id' => 1,
        ], $resource->toArray($user)['data']);

        $user->load('group');
        $this->assertSame([
            'id' => 1,
            'group' => [
                'id' => 1,
                'name' => 'vip',
            ],
        ], $resource->toArray($user)['data']);
    }
}
