<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\TestArticle;
use MiaoxingTest\Plugin\Fixture\TestCamelArticle;
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

        wei()->schema->table('test_articles_test_tags')
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
            ],
            [
                'name' => 'test',
            ],
        ]);

        wei()->db->insertBatch('test_profiles', [
            [
                'test_user_id' => 1,
                'description' => 'My name is twin',
            ],
            [
                'test_user_id' => 2,
                'description' => 'My name is admin',
            ],
        ]);

        wei()->db->insertBatch('test_tags', [
            [
                'name' => 'work',
            ],
            [
                'name' => 'life',
            ],
        ]);

        wei()->db->insertBatch('test_articles', [
            [
                'test_user_id' => 1,
                'title' => 'Article 1',
                'content' => 'Content 1',
            ],
            [
                'test_user_id' => 2,
                'title' => 'Article 2',
                'content' => 'Content 2',
            ],
            [
                'test_user_id' => 1,
                'title' => 'Article 3',
                'content' => 'Content 3',
            ],
        ]);

        wei()->db->insertBatch('test_articles_test_tags', [
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
            ],
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
        wei()->schema->dropIfExists('test_articles_test_tags');
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

        $profile = $user->profile;

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

        $users->findAll()->load('profile');

        $this->assertEquals($users[0]['id'], $users[0]->profile['test_user_id']);
        $this->assertEquals($users[1]['id'], $users[1]->profile['test_user_id']);
        $this->assertNull($users[2]->profile);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM test_users', $queries[0]);
        $this->assertEquals('SELECT * FROM test_profiles WHERE test_user_id IN (?, ?, ?)', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollHasOneLazyLoad()
    {
        /** @var TestUser|TestUser[] $users */
        $users = wei()->testUser();

        $users->findAll();

        $this->assertEquals($users[0]['id'], $users[0]->profile['test_user_id']);
        $this->assertEquals($users[1]['id'], $users[1]->profile['test_user_id']);
        $this->assertNull($users[2]->profile);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM test_users', $queries[0]);
        $this->assertEquals('SELECT * FROM test_profiles WHERE test_user_id = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM test_profiles WHERE test_user_id = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM test_profiles WHERE test_user_id = ? LIMIT 1', $queries[1]);
        $this->assertCount(4, $queries);
    }

    public function testRecordBelongsTo()
    {
        /** @var TestArticle $article */
        $article = wei()->testArticle();

        $article->findOneById(1);

        $user = $article->user;

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

        $articles->findAll()->load('user');

        foreach ($articles as $article) {
            $user = $article->user;
            $this->assertEquals($article['test_user_id'], $user['id']);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM test_articles', $queries[0]);
        $this->assertEquals('SELECT * FROM test_users WHERE id IN (?, ?)', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollBelongsToLazyLoad()
    {
        /** @var TestArticle|TestArticle[] $articles */
        $articles = wei()->testArticle();

        $articles->findAll();

        foreach ($articles as $article) {
            $user = $article->user;
            $this->assertEquals($article['test_user_id'], $user['id']);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM test_articles', $queries[0]);
        $this->assertEquals('SELECT * FROM test_users WHERE id = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM test_users WHERE id = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM test_users WHERE id = ? LIMIT 1', $queries[1]);
        $this->assertCount(4, $queries);
    }

    public function testRecordHasMany()
    {
        /** @var TestUser $user */
        $user = wei()->testUser();

        $user->findOneById(1);
        $articles = $user->articles;

        foreach ($articles as $article) {
            $this->assertEquals($article['test_user_id'], $user['id']);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM test_users WHERE id = ? LIMIT 1', $queries[0]);
        $this->assertEquals('SELECT * FROM test_articles WHERE test_user_id = ?', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testRecordHasManyWithQuery()
    {
        /** @var TestUser $user */
        $user = wei()->testUser();

        $user->findOneById(1);
        $articles = $user->customArticles()->andWhere('id >= ?', 1)->desc('id');

        foreach ($articles as $article) {
            $this->assertEquals($article['test_user_id'], $user['id']);
        }

        $this->assertEquals(2, $articles->length());
        $this->assertEquals(3, $articles[0]['id']);
        $this->assertEquals(1, $articles[1]['id']);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM test_users WHERE id = ? LIMIT 1', $queries[0]);
        $sql = 'SELECT * FROM test_articles';
        $sql .= ' WHERE ((test_user_id = ?) AND (title LIKE ?)) AND (id >= ?) ORDER BY id DESC, id DESC';
        $this->assertEquals($sql, $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollHasManyWithQuery()
    {
        /** @var TestUser|TestUser[] $users */
        $users = wei()->testUser();

        $users->findAll()->load('customArticles');

        foreach ($users as $user) {
            foreach ($user->customArticles as $article) {
                $this->assertEquals($article['test_user_id'], $user['id']);
            }
        }

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM test_users', $queries[0]);
        $this->assertEquals(
            'SELECT * FROM test_articles WHERE (test_user_id IN (?, ?, ?)) AND (title LIKE ?) ORDER BY id DESC',
            $queries[1]
        );
        $this->assertCount(2, $queries);
    }

    public function testRecordBelongsToMany()
    {
        /** @var TestArticle $article */
        $article = wei()->testArticle();

        $article->findOneById(1);

        $tags = $article->tags;

        $this->assertEquals('work', $tags[0]['name']);
        $this->assertEquals('life', $tags[1]['name']);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM test_articles WHERE id = ? LIMIT 1', $queries[0]);
        $sql = 'SELECT test_tags.* FROM test_tags';
        $sql .= ' INNER JOIN test_articles_test_tags';
        $sql .= ' ON test_articles_test_tags.test_tag_id = test_tags.id';
        $sql .= ' WHERE test_articles_test_tags.test_article_id = ?';
        $this->assertEquals($sql, $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testRecordBelongsToMany2()
    {
        /** @var TestTag $tag */
        $tag = wei()->testTag();

        $tag->findOneById(1);

        $articles = $tag->articles;

        $this->assertEquals('Article 1', $articles[0]['title']);
        $this->assertEquals('Article 2', $articles[1]['title']);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM test_tags WHERE id = ? LIMIT 1', $queries[0]);
        $sql = 'SELECT test_articles.* FROM test_articles';
        $sql .= ' INNER JOIN test_articles_test_tags';
        $sql .= ' ON test_articles_test_tags.test_article_id = test_articles.id';
        $sql .= ' WHERE test_articles_test_tags.test_tag_id = ?';
        $this->assertEquals($sql, $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollBelongsToMany()
    {
        /** @var TestArticle|TestArticle[] $articles */
        $articles = wei()->testArticle();

        $articles->findAll()->load('tags');

        foreach ($articles as $article) {
            foreach ($article->tags as $tag) {
                $this->assertInstanceOf(TestTag::className(), $tag);
            }
        }

        $this->assertEquals('work', $articles[0]->tags[0]['name']);
        $this->assertEquals('life', $articles[0]->tags[1]['name']);
        $this->assertEquals('work', $articles[1]->tags[0]['name']);
        $this->assertEquals('life', $articles[2]->tags[0]['name']);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM test_articles', $queries[0]);
        $sql = 'SELECT test_tags.*, test_articles_test_tags.test_article_id FROM test_tags';
        $sql .= ' INNER JOIN test_articles_test_tags';
        $sql .= ' ON test_articles_test_tags.test_tag_id = test_tags.id';
        $sql .= ' WHERE test_articles_test_tags.test_article_id IN (?, ?, ?)';
        $this->assertEquals($sql, $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollBelongsToManyWithQuery()
    {
        /** @var TestArticle|TestArticle[] $articles */
        $articles = wei()->testArticle();

        $articles->findAll()->load('customTags');

        foreach ($articles as $article) {
            foreach ($article->customTags as $tag) {
                $this->assertInstanceOf(TestTag::className(), $tag);
            }
        }

        $this->assertEquals('work', $articles[0]->customTags[0]['name']);
        $this->assertEquals('life', $articles[0]->customTags[1]['name']);
        $this->assertEquals('work', $articles[1]->customTags[0]['name']);
        $this->assertEquals('life', $articles[2]->customTags[0]['name']);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM test_articles', $queries[0]);
        $sql = 'SELECT test_tags.*, test_articles_test_tags.test_article_id FROM test_tags';
        $sql .= ' INNER JOIN test_articles_test_tags';
        $sql .= ' ON test_articles_test_tags.test_tag_id = test_tags.id';
        $sql .= ' WHERE (test_articles_test_tags.test_article_id IN (?, ?, ?)) AND (test_tags.id > ?)';
        $this->assertEquals($sql, $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testGetHasOneReturnFalse()
    {
        /** @var TestUser $user */
        $user = wei()->testUser();

        $user->findOneById(3);

        $this->assertEquals(null, $user->profile);
    }

    public function testNestedRelation()
    {
        /** @var TestArticle|TestArticle[] $articles */
        $articles = wei()->testArticle();

        $articles->findAll()->load('user.profile');

        $this->assertEquals(1, $articles[0]['id']);
        $this->assertEquals(1, $articles[0]->user['id']);
        $this->assertEquals(1, $articles[0]->user->profile['id']);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM test_articles', $queries[0]);
        $this->assertEquals('SELECT * FROM test_users WHERE id IN (?, ?)', $queries[1]);
        $this->assertEquals('SELECT * FROM test_profiles WHERE test_user_id IN (?, ?)', $queries[2]);
        $this->assertCount(3, $queries);
    }

    public function testLoadCache()
    {
        /** @var TestArticle|TestArticle[] $articles */
        $articles = wei()->testArticle();

        $articles->findAll()->load('user')->load('user');

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM test_articles', $queries[0]);
        $this->assertEquals('SELECT * FROM test_users WHERE id IN (?, ?)', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testEmptyLocalKeyDoNotExecuteQuery()
    {
        wei()->db->insert('test_articles', [
            'test_user_id' => 0,
            'title' => 'Article 4',
            'content' => 'Content 4',
        ]);
        $id = wei()->db->lastInsertId();
        wei()->db->setOption('queries', []);

        /** @var TestArticle $article */
        $article = wei()->testArticle();

        $article->findOneById($id);
        $user = $article->user;
        $this->assertNull($user);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM test_articles WHERE id = ? LIMIT 1', $queries[0]);
        $this->assertCount(1, $queries);
    }

    public function testNewRecordsRecordIsNull()
    {
        /** @var TestUser $user */
        $user = wei()->testUser();

        $profile = $user->profile;

        $this->assertNull($profile);
    }

    public function testNewRecordsCollIsNotNull()
    {
        /** @var TestUser $user */
        $user = wei()->testUser();

        $articles = $user->articles;

        $this->assertNotNull($articles);
        $this->assertInstanceOf(TestArticle::className(), $articles);
    }

    public function testCamelFromArray()
    {
        $article = wei()->testCamelArticle();

        $article->fromArray([
            'testUserId' => 1,
        ]);

        $this->assertEquals(1, $article['testUserId']);
    }

    public function testCamelFromArrayIgnoreSnake()
    {
        $article = wei()->testCamelArticle();

        $article->fromArray([
            'test_user_id' => 1,
        ]);

        $this->assertNull($article['testUserId']);
        $this->assertNull($article['test_user_id']);

        $article->fromArray([
            'test_user_id' => 1,
            'testUserId' => 2,
        ]);

        $this->assertEquals(2, $article['testUserId']);
    }

    public function testCamelToArray()
    {
        $article = wei()->testCamelArticle();

        $article->fromArray([
            'testUserId' => 1,
        ]);

        $this->assertEquals([
            'testUserId' => 1,
            'id' => null,
            'title' => null,
            'content' => null,
        ], $article->toArray());
    }

    public function testCamelGetSet()
    {
        $article = wei()->testCamelArticle();

        $this->assertEquals(null, $article['testUserId']);

        $article['testUserId'] = 1;

        $this->assertEquals(1, $article['testUserId']);
    }

    public function testCastGet()
    {
        $article = wei()->testCamelArticle();

        $this->assertNull($article['id']);

        $article->fromArray([
            'id' => '1',
            'testUserId' => '2',
            'title' => 'title',
        ]);

        $this->assertSame(1, $article['id']);
        $this->assertSame(2, $article['testUserId']);
        $this->assertSame('title', $article['title']);
        $this->assertNull($article['content']);
    }

    public function testCastSet()
    {
        /** @var TestCamelArticle $article */
        $article = wei()->testCamelArticle();

        $article->fromArray([
            'id' => '1',
            'testUserId' => '2',
            'title' => 'title',
        ]);

        $data = $article->getData();
        $this->assertSame(1, $data['id']);
        $this->assertSame(2, $data['test_user_id']);
        $this->assertSame('title', $data['title']);
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
