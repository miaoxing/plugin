<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\App\Fixture\Controller\Test;
use MiaoxingTest\Plugin\Fixture\TestArticle;
use MiaoxingTest\Plugin\Fixture\TestTag;
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
            ->int('test_user_id')
            ->string('description')
            ->exec();

        wei()->schema->table('test_articles')
            ->id()
            ->int('test_user_id')
            ->string('title', 128)
            ->text('content')
            ->exec();

        wei()->schema->table('test_tags')
            ->id()
            ->string('name')
            ->exec();

        wei()->schema->table('test_article_test_tag')
            ->id()
            ->int('test_article_id')
            ->int('test_tag_id')
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
                'test_user_id' => '1',
                'description' => 'My name is twin',
            ],
            [
                'test_user_id' => '2',
                'description' => 'My name is admin',
            ]
        ]);

        wei()->db->insertBatch('test_tags', [
            [
                'name' => 'work',
            ],
            [
                'name' => 'life'
            ]
        ]);

        wei()->db->insertBatch('test_articles', [
            [
                'test_user_id' => '1',
                'title' => 'Article 1',
                'content' => 'Content 1',
            ],
            [
                'test_user_id' => '2',
                'title' => 'Article 2',
                'content' => 'Content 2',
            ],
            [
                'test_user_id' => '1',
                'title' => 'Article 3',
                'content' => 'Content 3',
            ]
        ]);

        wei()->db->insertBatch('test_article_test_tag', [
            [
                'test_article_id' => 1,
                'test_tag_id' => 1,
            ],
            [
                'test_article_id' => 1,
                'test_tag_id' => 2,
            ],
            [
                'test_article_id' => 2,
                'test_tag_id' => 1,
            ],
            [
                'test_article_id' => 3,
                'test_tag_id' => 2,
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
        wei()->schema->dropIfExists('test_tags');
        wei()->schema->dropIfExists('test_article_test_tag');
    }

    public function setUp()
    {
        parent::setUp();

        $this->clearLogs();
    }

    public function testRecordHasOne()
    {
        /** @var TestUser $user */
        $user = wei()->testUser();

        $user->findOneById(1);

        $profile = $user->getProfile();

        $this->assertEquals(1, $profile['test_user_id']);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM test_users WHERE id = ? LIMIT 1', $queries[0]);
        $this->assertEquals('SELECT * FROM test_profiles WHERE test_user_id = ? LIMIT 1', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollHasOne()
    {
        /** @var TestUser|TestUser[] $users */
        $users = wei()->testUser();

        $users->findAll()->includes('profile');

        foreach ($users as $user) {
            $profile = $user->getProfile();
            $this->assertEquals($profile['test_user_id'], $user['id']);
        }

        $queries = wei()->db->getQueries();

        $this->assertEquals("SELECT * FROM test_users", $queries[0]);
        $this->assertEquals("SELECT * FROM test_profiles WHERE test_user_id IN (?, ?)", $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testRecordBelongsTo()
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

    public function testCollBelongsTo()
    {
        /** @var TestArticle|TestArticle[] $articles */
        $articles = wei()->testArticle();

        $articles->findAll()->includes('user');

        foreach ($articles as  $article) {
            $user = $article->getUser();
            $this->assertEquals($article['test_user_id'], $user['id']);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals("SELECT * FROM test_articles", $queries[0]);
        $this->assertEquals("SELECT * FROM test_users WHERE id IN (?, ?)", $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testRecordHasMany()
    {
        /** @var TestUser $user */
        $user = wei()->testUser();

        $user->findOneById(1);
        $articles = $user->getArticles();

        foreach ($articles as $article) {
            $this->assertEquals($article['test_user_id'], $user['id']);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals("SELECT * FROM test_users WHERE id = ? LIMIT 1", $queries[0]);
        $this->assertEquals("SELECT * FROM test_articles WHERE test_user_id = ?", $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollHasMany()
    {
        /** @var TestUser|TestUser[] $users */
        $users = wei()->testUser();

        $users->findAll()->includes('articles');

        foreach ($users as $user) {
            foreach ($user->getArticles() as $article) {
                $this->assertEquals($article['test_user_id'], $user['id']);
            }
        }

        $queries = wei()->db->getQueries();

        $this->assertEquals("SELECT * FROM test_users", $queries[0]);
        $this->assertEquals("SELECT * FROM test_articles WHERE test_user_id IN (?, ?)", $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testRecordBelongsToMany()
    {
        /** @var TestArticle $article */
        $article = wei()->testArticle();

        $article->findOneById(1);

        $tags = $article->getTags();

        $this->assertEquals('work', $tags[0]['name']);
        $this->assertEquals('life', $tags[1]['name']);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM test_articles WHERE id = ? LIMIT 1', $queries[0]);
        $sql = 'SELECT test_tags.* FROM test_tags'
            . ' INNER JOIN test_article_test_tag'
            . ' ON test_article_test_tag.test_tag_id = test_tags.id'
            . ' WHERE test_article_test_tag.test_article_id = ?';
        $this->assertEquals($sql, $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testRecordBelongsToManyReverse()
    {
        /** @var TestTag $tag */
        $tag = wei()->testTag();

        $tag->findOneById(1);

        $articles = $tag->getArticles();

        $this->assertEquals('Article 1', $articles[0]['title']);
        $this->assertEquals('Article 2', $articles[1]['title']);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM test_tags WHERE id = ? LIMIT 1', $queries[0]);
        $sql = 'SELECT test_articles.* FROM test_articles'
            . ' INNER JOIN test_article_test_tag'
            . ' ON test_article_test_tag.test_article_id = test_articles.id'
            . ' WHERE test_article_test_tag.test_tag_id = ?';
        $this->assertEquals($sql, $queries[1]);
        $this->assertCount(2, $queries);
    }

    protected function clearLogs()
    {
        // preload fields cache
        wei()->testUser()->getFields();
        wei()->testArticle()->getFields();
        wei()->testProfile()->getFields();
        wei()->testTag()->getFields();

        wei()->db->setOption('queries', []);
    }
}

