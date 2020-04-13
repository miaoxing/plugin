<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\DbTrait;
use MiaoxingTest\Plugin\Model\Fixture\TestArticle;
use MiaoxingTest\Plugin\Model\Fixture\TestProfile;
use MiaoxingTest\Plugin\Model\Fixture\TestTag;
use MiaoxingTest\Plugin\Model\Fixture\TestUser;

/**
 * 数据库关联测试
 */
class RelationTest extends BaseTestCase
{
    use DbTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::dropRelationTables();
        static::createRelationTables();

        wei()->db->batchInsert('test_users', [
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

        wei()->db->batchInsert('test_profiles', [
            [
                'test_user_id' => 1,
                'description' => 'My name is twin',
            ],
            [
                'test_user_id' => 2,
                'description' => 'My name is admin',
            ],
        ]);

        wei()->db->batchInsert('test_tags', [
            [
                'name' => 'work',
            ],
            [
                'name' => 'life',
            ],
        ]);

        wei()->db->batchInsert('test_articles', [
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

        wei()->db->batchInsert('test_articles_test_tags', [
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

    public static function tearDownAfterClass(): void
    {
        static::dropRelationTables();
        parent::tearDownAfterClass();
    }

    private static function createRelationTables()
    {
        static::createTables();

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
    }

    private static function dropRelationTables()
    {
        static::dropTables();
        wei()->schema->dropIfExists('test_profiles');
        wei()->schema->dropIfExists('test_articles');
        wei()->schema->dropIfExists('test_tags');
        wei()->schema->dropIfExists('test_articles_test_tags');
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->clearLogs();
    }

    public function testRecordHasOne()
    {
        $user = TestUser::new();

        $user->find(1);

        $profile = $user->profile;

        $this->assertEquals(1, $profile->testUserId);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` = ? LIMIT 1', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollHasOne()
    {
        $users = TestUser::new();

        $users->all()->load('profile');

        $this->assertEquals($users[0]->id, $users[0]->profile->testUserId);
        $this->assertEquals($users[1]->id, $users[1]->profile->testUserId);
        $this->assertNull($users[2]->profile);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM `test_users`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` IN (?, ?, ?)', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollHasOneLazyLoad()
    {
        $users = TestUser::new();

        $users->all();

        $this->assertEquals($users[0]->id, $users[0]->profile->testUserId);
        $this->assertEquals($users[1]->id, $users[1]->profile->testUserId);
        $this->assertNull($users[2]->profile);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM `test_users`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` = ? LIMIT 1', $queries[1]);
        $this->assertCount(4, $queries);
    }

    public function testRecordBelongsTo()
    {
        $article = TestArticle::new();

        $article->find(1);

        $user = $article->user;

        $this->assertEquals(1, $user->id);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM `test_articles` WHERE `id` = ? LIMIT 1', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollBelongsTo()
    {
        $articles = TestArticle::new();

        $articles->all()->load('user');

        foreach ($articles as $article) {
            $user = $article->user;
            $this->assertEquals($article->testUserId, $user->id);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` IN (?, ?)', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollBelongsToLazyLoad()
    {
        $articles = TestArticle::new();

        $articles->all();

        foreach ($articles as $article) {
            $user = $article->user;
            $this->assertEquals($article->testUserId, $user->id);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[1]);
        $this->assertCount(4, $queries);
    }

    public function testRecordHasMany()
    {
        $user = TestUser::new();

        $user->find(1);
        $articles = $user->articles;

        foreach ($articles as $article) {
            $this->assertEquals($article->testUserId, $user->id);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_articles` WHERE `test_user_id` = ?', $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testRecordHasManyWithQuery()
    {
        $user = TestUser::new();

        $user->find(1);
        /** @var TestArticle|TestArticle[] $articles */
        $articles = $user->customArticles()->where('id', '>=', 1)->desc('id');

        foreach ($articles as $article) {
            $this->assertEquals($article->testUserId, $user->id);
        }

        $this->assertCount(2, $articles);
        $this->assertEquals(3, $articles[0]->id);
        $this->assertEquals(1, $articles[1]->id);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[0]);
        $this->assertEquals(implode(' ', [
            'SELECT * FROM `test_articles` WHERE `test_user_id` = ? AND `title` LIKE ? AND `id` >= ?',
            'ORDER BY `id` DESC, `id` DESC',
        ]), $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollHasManyWithQuery()
    {
        $users = TestUser::newColl();

        $users->all()->load('customArticles');

        foreach ($users as $user) {
            foreach ($user->customArticles as $article) {
                $this->assertEquals($article->testUserId, $user->id);
            }
        }

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM `test_users`', $queries[0]);
        $this->assertEquals(
            'SELECT * FROM `test_articles` WHERE `test_user_id` IN (?, ?, ?) AND `title` LIKE ? ORDER BY `id` DESC',
            $queries[1]
        );
        $this->assertCount(2, $queries);
    }

    public function testRecordBelongsToMany()
    {
        $article = TestArticle::find(1);

        $tags = $article->tags;

        $this->assertEquals('work', $tags[0]->name);
        $this->assertEquals('life', $tags[1]->name);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM `test_articles` WHERE `id` = ? LIMIT 1', $queries[0]);

        $this->assertEquals(implode(' ', [
            'SELECT `test_tags`.* FROM `test_tags`',
            'INNER JOIN `test_articles_test_tags` ON `test_articles_test_tags`.`test_tag_id` = `test_tags`.`id`',
            'WHERE `test_articles_test_tags`.`test_article_id` = ?',
        ]), $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testRecordBelongsToMany2()
    {
        $tag = TestTag::find(1);

        $articles = $tag->articles;

        $this->assertEquals('Article 1', $articles[0]->title);
        $this->assertEquals('Article 2', $articles[1]->title);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM `test_tags` WHERE `id` = ? LIMIT 1', $queries[0]);
        $this->assertEquals(implode(' ', [
            'SELECT `test_articles`.* FROM `test_articles` INNER JOIN',
            '`test_articles_test_tags` ON `test_articles_test_tags`.`test_article_id` = `test_articles`.`id`',
            'WHERE `test_articles_test_tags`.`test_tag_id` = ?',
        ]), $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollBelongsToMany()
    {
        $articles = TestArticle::newColl();

        $articles->all()->load('tags');

        foreach ($articles as $article) {
            foreach ($article->tags as $tag) {
                $this->assertInstanceOf(TestTag::class, $tag);
            }
        }

        $this->assertEquals('work', $articles[0]->tags[0]->name);
        $this->assertEquals('life', $articles[0]->tags[1]->name);
        $this->assertEquals('work', $articles[1]->tags[0]->name);
        $this->assertEquals('life', $articles[2]->tags[0]->name);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles`', $queries[0]);
        $this->assertEquals(implode(' ', [
            'SELECT `test_tags`.*, `test_articles_test_tags`.`test_article_id` FROM `test_tags`',
            'INNER JOIN `test_articles_test_tags` ON `test_articles_test_tags`.`test_tag_id` = `test_tags`.`id`',
            'WHERE `test_articles_test_tags`.`test_article_id` IN (?, ?, ?)',
        ]), $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testCollBelongsToManyWithQuery()
    {
        $articles = TestArticle::newColl();

        $articles->all()->load('customTags');

        foreach ($articles as $article) {
            foreach ($article->customTags as $tag) {
                $this->assertInstanceOf(TestTag::class, $tag);
            }
        }

        $this->assertEquals('work', $articles[0]->customTags[0]->name);
        $this->assertEquals('life', $articles[0]->customTags[1]->name);
        $this->assertEquals('work', $articles[1]->customTags[0]->name);
        $this->assertEquals('life', $articles[2]->customTags[0]->name);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles`', $queries[0]);
        $this->assertEquals(implode(' ', [
            'SELECT `test_tags`.*, `test_articles_test_tags`.`test_article_id` FROM `test_tags`',
            'INNER JOIN `test_articles_test_tags` ON `test_articles_test_tags`.`test_tag_id` = `test_tags`.`id`',
            'WHERE `test_articles_test_tags`.`test_article_id` IN (?, ?, ?) AND `test_tags`.`id` > ?'
        ]), $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testGetHasOneReturnFalse()
    {
        $user = TestUser::new();

        $user->find(3);

        $this->assertEquals(null, $user->profile);
    }

    public function testNestedRelation()
    {
        $articles = TestArticle::new();

        $articles->all()->load('user.profile');

        $this->assertEquals(1, $articles[0]->id);
        $this->assertEquals(1, $articles[0]->user->id);
        $this->assertEquals(1, $articles[0]->user->profile->id);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` IN (?, ?)', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` IN (?, ?)', $queries[2]);
        $this->assertCount(3, $queries);
    }

    public function testLoadCache()
    {
        /** @var TestArticle|TestArticle[] $articles */
        $articles = TestArticle::new();

        $articles->all()->load('user')->load('user');

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` IN (?, ?)', $queries[1]);
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

        $article = TestArticle::new();

        $article->find($id);
        $user = $article->user;
        $this->assertNull($user);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles` WHERE `id` = ? LIMIT 1', $queries[0]);
        $this->assertCount(1, $queries);
    }

    public function testNewRecordsRecordIsNull()
    {
        /** @var TestUser $user */
        $user = TestUser::new();

        $profile = $user->profile;

        $this->assertNull($profile);
    }

    public function testNewRecordsCollIsNotNull()
    {
        $user = TestUser::new();

        $articles = $user->articles;

        $this->assertNotNull($articles);
        $this->assertInstanceOf(TestArticle::class, $articles);
    }

    public function testSetHiddenByString()
    {
        $article = TestArticle::new();

        $array = $article->setHidden('id')->toArray();

        $this->assertArrayNotHasKey('id', $array);
        $this->assertArrayHasKey('testUserId', $array);
    }

    public function testSetHiddenByArray()
    {
        $article = TestArticle::new();

        $array = $article->setHidden(['id', 'test_user_id'])->toArray();

        $this->assertArrayNotHasKey('id', $array);
        $this->assertArrayNotHasKey('test_user_id', $array);
    }

    protected function clearLogs()
    {
        // preload fields cache
        TestUser::getFields();
        TestArticle::getFields();
        TestProfile::getFields();
        TestTag::getFields();

        wei()->db->setOption('queries', []);
    }
}
