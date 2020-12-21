<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\DbTrait;
use MiaoxingTest\Plugin\Model\Fixture\TestUser;

class AttributeTest extends BaseTestCase
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

    public function testOffsetExists()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);

        $this->assertTrue(isset($user['name']));
        $this->assertTrue(isset($user['group_id']));
        $this->assertFalse(isset($user['key2']));
    }

    public function testOffsetGet()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);

        $this->assertSame('twin', $user['name']);
        $this->assertSame(0, $user['group_id']);
    }

    public function testOffsetGetInvalid()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);

        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: key'));
        $user['key'];
    }

    public function testOffsetSet()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);

        $user['name'] = 'abc';
        $this->assertSame('abc', $user['name']);
    }

    public function testOffsetSetInvalid()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: key'));

        $user = TestUser::new();
        $user['key'] = 'test';
    }

    public function testOffsetUnset()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);
        $this->assertSame('twin', $user['name']);

        unset($user['name']);
        $this->assertNull($user['name']);
    }

    public function testGet()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);
        $this->assertSame('twin', $user->get('name'));
        $this->assertSame(0, $user->get('group_id'));
    }

    public function testGetInvalid()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);

        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: key2'));
        $this->assertNull($user->get('key2'));
    }

    public function testSet()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);
        $this->assertSame('twin', $user->get('name'));

        $user->set('group_id', 1);
        $this->assertSame(1, $user->get('group_id'));
    }

    public function testSetInvalid()
    {
        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: key'));

        $user = TestUser::new([
            'name' => 'twin',
        ]);
        $user->set('key', 'test');
    }

    public function testSetModelAsColl()
    {
        $this->initFixtures();

        $user = TestUser::new();

        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: [null]'));
        $user[] = TestUser::new();
    }

    public function testMagicGet()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);
        $this->assertSame('twin', $user->name);
        $this->assertSame(0, $user->group_id);
    }

    public function testMagicGetInvalid()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessageMatches('/Property or object "key2" \(class "Wei\\\Key2"\) not found, called in file/');
        $this->assertNull($user->key2);
    }

    public function testMagicSet()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);

        $user->group_id = 1;
        $this->assertSame(1, $user->group_id);
    }

    public function testMagicSetInvalid()
    {
        $this->initFixtures();

        $user = TestUser::new([
            'name' => 'twin',
        ]);

        $this->expectExceptionObject(new \InvalidArgumentException('Invalid property: key'));
        $user->key = 'abc';
    }

    public function testMagicIsset()
    {
        $this->initFixtures();

        $user = TestUser::new();

        $this->assertTrue($user->id ?? true);

        $user->id = 123;
        $this->assertTrue($user->id ?? true);

        $this->assertTrue($user->notFound ?? true);
    }
}
