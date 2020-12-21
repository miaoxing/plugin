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
 *
 * @internal
 * @mixin \DbMixin
 */
final class RelationTest extends BaseTestCase
{
    use DbTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::setTablePrefix();
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
        static::resetTablePrefix();
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearLogs();
    }

    public function testModelHasOne()
    {
        $user = TestUser::new();

        $user->find(1);

        $profile = $user->profile;

        $this->assertEquals(1, $profile->test_user_id);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` = ? LIMIT 1', $queries[1]);
        $this->assertCount(2, $queries);

        $array = $user->toArray();
        $this->assertArrayHasKey('profile', $array);
        $this->assertEquals(1, $array['profile']['test_user_id']);
    }

    public function testCollHasOne()
    {
        $users = TestUser::new();

        $users->all()->load('profile');

        $this->assertEquals($users[0]->id, $users[0]->profile->test_user_id);
        $this->assertEquals($users[1]->id, $users[1]->profile->test_user_id);
        $this->assertNull($users[2]->profile);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM `test_users`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` IN (?, ?, ?)', $queries[1]);
        $this->assertCount(2, $queries);

        $array = $users->toArray();
        $this->assertArrayHasKey('profile', $array[0]);
        $this->assertEquals(1, $array[0]['profile']['test_user_id']);
    }

    public function testCollectionHasOneLazyLoad()
    {
        $users = TestUser::new();

        $users->all();

        $this->assertEquals($users[0]->id, $users[0]->profile->test_user_id);
        $this->assertEquals($users[1]->id, $users[1]->profile->test_user_id);
        $this->assertNull($users[2]->profile);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM `test_users`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` = ? LIMIT 1', $queries[1]);
        $this->assertCount(4, $queries);

        $array = $users->toArray();
        $this->assertArrayHasKey('profile', $array[0]);
        $this->assertEquals(1, $array[0]['profile']['test_user_id']);
    }

    public function testModelBelongsTo()
    {
        $article = TestArticle::new();

        $article->find(1);

        $user = $article->user;

        $this->assertEquals(1, $user->id);

        $queries = wei()->db->getQueries();

        $this->assertEquals('SELECT * FROM `test_articles` WHERE `id` = ? LIMIT 1', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[1]);
        $this->assertCount(2, $queries);

        $array = $article->toArray();
        $this->assertArrayHasKey('user', $array);
        $this->assertEquals(1, $array['user']['id']);
    }

    public function testCollBelongsTo()
    {
        $articles = TestArticle::new();

        $articles->all()->load('user');

        foreach ($articles as $article) {
            $user = $article->user;
            $this->assertEquals($article->test_user_id, $user->id);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` IN (?, ?)', $queries[1]);
        $this->assertCount(2, $queries);

        $array = $articles->toArray();
        $this->assertArrayHasKey('user', $array[0]);
        $this->assertEquals(1, $array[0]['user']['id']);
    }

    public function testCollBelongsToLazyLoad()
    {
        $articles = TestArticle::new();

        $articles->all();

        foreach ($articles as $article) {
            $user = $article->user;
            $this->assertEquals($article->test_user_id, $user->id);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[1]);
        $this->assertCount(4, $queries);

        $array = $articles->toArray();
        $this->assertArrayHasKey('user', $array[0]);
        $this->assertEquals(1, $array[0]['user']['id']);
    }

    public function testModelHasMany()
    {
        $user = TestUser::new();

        $user->find(1);
        $articles = $user->articles;

        foreach ($articles as $article) {
            $this->assertEquals($article->test_user_id, $user->id);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_articles` WHERE `test_user_id` = ?', $queries[1]);
        $this->assertCount(2, $queries);

        $array = $user->toArray();
        $this->assertArrayHasKey('articles', $array);
        $this->assertEquals(1, $array['articles'][0]['id']);
    }

    public function testCollHasMany()
    {
        $users = TestUser::all();
        $this->assertNotNull($users[0]->id);

        $users->load('articles');
        $this->assertNotNull($users[0]->articles[0]->id);
    }

    public function testModelHasManyWithQuery()
    {
        $user = TestUser::new();

        $user->find(1);
        /** @var TestArticle|TestArticle[] $articles */
        $articles = $user->customArticles()->where('id', '>=', 1)->desc('id')->all();

        foreach ($articles as $article) {
            $this->assertEquals($article->test_user_id, $user->id);
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
                $this->assertEquals($article->test_user_id, $user->id);
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

    public function testModelBelongsToMany()
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

        $array = $article->toArray();
        $this->assertArrayHasKey('tags', $array);
        $this->assertEquals(1, $array['tags'][0]['id']);
    }

    public function testModelBelongsToMany2()
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

        $array = $articles->toArray();
        $this->assertArrayHasKey('tags', $array[0]);
        $this->assertEquals(1, $array[0]['tags'][0]['id']);
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
            'WHERE `test_articles_test_tags`.`test_article_id` IN (?, ?, ?) AND `test_tags`.`id` > ?',
        ]), $queries[1]);
        $this->assertCount(2, $queries);
    }

    public function testGetHasOneReturnsNull()
    {
        $user = TestUser::new();

        $user->find(3);

        $this->assertNull($user->profile);

        $array = $user->toArray();
        $this->assertArrayHasKey('profile', $array);
        $this->assertNull($user['profile']);
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

        $array = $articles->toArray();
        $this->assertArrayHasKey('user', $array[0]);
        $this->assertArrayHasKey('profile', $array[0]['user']);
        $this->assertEquals(1, $array[0]['user']['profile']['id']);
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
        // @phpstan-ignore-next-line user 识别为了 UserMixin 的 Miaoxing\Plugin\Service\User
        $this->assertNull($user);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles` WHERE `id` = ? LIMIT 1', $queries[0]);
        $this->assertCount(1, $queries);
    }

    public function testNewModelsModelIsNull()
    {
        /** @var TestUser $user */
        $user = TestUser::new();

        $profile = $user->profile;

        $this->assertNull($profile);
    }

    public function testNewModelsCollIsNotNull()
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
        $this->assertArrayHasKey('test_user_id', $array);
    }

    public function testSetHiddenByArray()
    {
        $article = TestArticle::new();

        $array = $article->setHidden(['id', 'test_user_id'])->toArray();

        $this->assertArrayNotHasKey('id', $array);
        $this->assertArrayNotHasKey('test_user_id', $array);
    }

    /**
     * @group saveRelation
     */
    public function testSaveRelationCreateOne()
    {
        $user = TestUser::save();

        /** @var TestProfile $profile */
        $profile = $user->profile()->saveRelation();

        $this->assertSame($user->id, $profile->test_user_id);
    }

    /**
     * @group saveRelation
     */
    public function testSaveRelationUpdateOne()
    {
        $user = TestUser::save([
            'name' => 'test',
        ]);

        $profile = TestProfile::save([
            'test_user_id' => $user->id,
        ]);

        $profile2 = $user->profile()->saveRelation([
            'description' => 'test',
        ]);

        $this->assertSame($profile->id, $profile2->id);
        $this->assertSame('test', $profile->reload()->description);
    }

    /**
     * @group saveRelation
     */
    public function testSaveRelationIgnoreRelateId()
    {
        $user = TestUser::save([
            'name' => 'test',
        ]);

        /** @var TestProfile $profile */
        $profile = $user->profile()->saveRelation([
            'test_user_id' => $user->id + 1,
            'description' => 'test',
        ]);

        $this->assertSame($user->id, $profile->test_user_id);
    }

    /**
     * @group saveRelation
     */
    public function testSaveRelationCreateMany()
    {
        $user = TestUser::save();

        /** @var TestArticle|TestArticle[] $articles */
        $articles = $user->articles()->saveRelation([
            [
                'title' => 'title 1',
            ],
            [
                'title' => 'title 2',
            ],
        ]);

        $this->assertCount(2, $articles);
        $this->assertSame($user->id, $articles[0]->test_user_id);
    }

    /**
     * @group saveRelation
     */
    public function testSaveRelationUpdateMany()
    {
        $user = TestUser::save();

        $articles = TestArticle::newColl()->save([
            TestArticle::new([
                'test_user_id' => $user->id,
            ]),
            TestArticle::new([
                'test_user_id' => $user->id,
            ]),
        ]);

        $articles2 = $user->articles()->saveRelation([
            [
                'id' => $articles[0]->id,
                'title' => 'title 1',
            ],
            [
                'id' => $articles[1]->id,
                'title' => 'title 2',
            ],
        ]);

        $this->assertSame($articles[0]->id, $articles2[0]->id);

        $articles = $user->articles;
        $this->assertSame('title 1', $articles[0]->title);
    }

    public function testSaveRelationDeleteMany()
    {
        $user = TestUser::save();

        TestArticle::save([
            TestArticle::new([
                'test_user_id' => $user->id,
            ]),
            TestArticle::new([
                'test_user_id' => $user->id,
            ]),
        ]);

        $articles2 = $user->articles()->saveRelation();

        $this->assertCount(0, $articles2);

        $articles = $user->articles;
        $this->assertCount(0, $articles);
    }

    protected function clearLogs()
    {
        // preload fields cache
        TestUser::getColumns();
        TestArticle::getColumns();
        TestProfile::getColumns();
        TestTag::getColumns();

        wei()->db->setOption('queries', []);
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
            ->string('content')
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
}
