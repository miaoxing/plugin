<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\App\Fixture\Controller\Test;
use MiaoxingTest\Plugin\Fixture\TestArticle;
use MiaoxingTest\Plugin\Fixture\TestUser;

/**
 * 数据库记录测试
 */
class RecordTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::dropTables();
        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Plugin\Fixture');

        wei()->schema->table('test_users')
            ->id()
            ->string('name', 32)
            ->exec();

        wei()->schema->table('test_profiles')
            ->id()
            ->int('user_id')
            ->string('description')
            ->exec();

        wei()->schema->table('test_articles')
            ->id()
            ->int('user_id')
            ->string('title', 128)
            ->text('content')
            ->exec();

        wei()->db->insertBatch('test_users', [
            [
                'name' => 'twin',
            ],
            [
                'name' => 'admin',
            ]
        ]);

        wei()->db->insertBatch('test_profiles', [
            [
                'user_id' => '1',
                'description' => 'My name is twin',
            ],
            [
                'user_id' => '2',
                'description' => 'My name is admin',
            ]
        ]);

        wei()->db->insertBatch('test_articles', [
            [
                'user_id' => '1',
                'title' => 'Article 1',
                'content' => 'Content 1',
            ],
            [
                'user_id' => '1',
                'title' => 'Article 2',
                'content' => 'Content 2',
            ],
            [
                'user_id' => '1',
                'title' => 'Article 3',
                'content' => 'Content 3',
            ]
        ]);
    }

    public static function tearDownAfterClass()
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_users');
        wei()->schema->dropIfExists('test_profiles');
        wei()->schema->dropIfExists('test_articles');
    }

    public function setUp()
    {
        parent::setUp();

        $this->clearLogs();
    }

    public function testHasOne()
    {
        /** @var TestUser $user */
        $user = wei()->testUser();

        $user->findOneById(1);

        $profile = $user->getProfile();

        $this->assertEquals(1, $profile['user_id']);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM test_users WHERE id = ? LIMIT 1', $queries[0]);
        $this->assertEquals('SELECT * FROM test_profiles WHERE user_id = ? LIMIT 1', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testBelongsTo()
    {
        /** @var TestArticle $article */
        $article = wei()->testArticle();

        $article->findOneById(1);

        $user = $article->getUser();

        $this->assertEquals(1, $user['id']);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM test_articles WHERE id = ? LIMIT 1', $queries[0]);
        $this->assertEquals('SELECT * FROM test_users WHERE id = ? LIMIT 1', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testHasMany()
    {
        /** @var TestUser|TestUser[] $users */
        $users = wei()->testUser();

        $users->findAll()->includes('articles');

        foreach ($users as $user) {
            foreach ($user->getArticles() as $article) {
                $this->assertEquals($article['user_id'], $user['id']);
            }
        }

        $queries = wei()->db->getQueries();

        $this->assertEquals("SELECT * FROM test_users", $queries[0]);
        $this->assertEquals("SELECT * FROM test_articles WHERE user_id IN (?, ?)", $queries[1]);
        $this->assertCount(2, $queries);
    }

    protected function clearLogs()
    {
        // preload fields cache
        wei()->testUser()->getFields();
        wei()->testArticle()->getFields();
        wei()->testProfile()->getFields();

        wei()->db->setOption('queries', []);
    }
}

