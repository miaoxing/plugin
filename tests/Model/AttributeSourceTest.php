<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\DbTrait;
use MiaoxingTest\Plugin\Model\Fixture\TestUser;
use ReflectionMethod;

/**
 * @phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
 */
class AttributeSourceTest extends BaseTestCase
{
    use DbTrait;

    protected const ATTRIBUTE_SOURCE_USER = 1;

    protected const ATTRIBUTE_SOURCE_DB = 2;

    protected const ATTRIBUTE_SOURCE_PHP = 3;

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

    public function testNew()
    {
        $this->initFixtures();

        $user = TestUser::new();
        $getAttributeSource = $this->createAttributeSource($user);

        $this->assertSame(static::ATTRIBUTE_SOURCE_USER, $getAttributeSource('name'));
        $this->assertSame(static::ATTRIBUTE_SOURCE_USER, $getAttributeSource('*'));

        $user->name = 'test';
        $this->assertSame(static::ATTRIBUTE_SOURCE_USER, $getAttributeSource('name'));

        $groupId = $user->group_id;
        $this->assertSame(0, $groupId);
        $this->assertSame(static::ATTRIBUTE_SOURCE_PHP, $getAttributeSource('group_id'));

        return $user;
    }

    public function testNewWithAttributes()
    {
        $this->initFixtures();

        $user = TestUser::new(['name' => 'test']);
        $getAttributeSource = $this->createAttributeSource($user);

        $this->assertSame(static::ATTRIBUTE_SOURCE_USER, $getAttributeSource('*'));

        $this->assertSame(static::ATTRIBUTE_SOURCE_USER, $getAttributeSource('name'));
    }

    public function testFindFromDb()
    {
        $this->initFixtures();

        $user = TestUser::first();
        $getAttributeSource = $this->createAttributeSource($user);

        $this->assertSame(static::ATTRIBUTE_SOURCE_DB, $getAttributeSource('name'));
        $this->assertSame(static::ATTRIBUTE_SOURCE_DB, $getAttributeSource('*'));

        $user->name = 'test';
        $this->assertSame(static::ATTRIBUTE_SOURCE_USER, $getAttributeSource('name'));

        $name = $user->name;
        $this->assertSame('test', $name);
        $this->assertSame(static::ATTRIBUTE_SOURCE_PHP, $getAttributeSource('name'));

        // Get it again, the data source is still php
        $name = $user->name;
        $this->assertSame('test', $name);
        $this->assertSame(static::ATTRIBUTE_SOURCE_PHP, $getAttributeSource('name'));
    }

    protected function createAttributeSource($user)
    {
        $method = new ReflectionMethod($user, 'getAttributeSource');
        $method->setAccessible(true);
        return function ($name) use ($user, $method) {
            return $method->invoke($user, $name);
        };
    }
}
