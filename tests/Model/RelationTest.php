<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\DbTrait;
use MiaoxingTest\Plugin\Model\Fixture\TestArticle;
use MiaoxingTest\Plugin\Model\Fixture\TestProfile;
use MiaoxingTest\Plugin\Model\Fixture\TestTag;
use MiaoxingTest\Plugin\Model\Fixture\TestUser;
use MiaoxingTest\Plugin\Model\Fixture\TestUserGroup;

/**
 * 数据库关联测试
 *
 * @internal
 * @mixin \DbMixin
 * @phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
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
                'group_id' => 1,
            ],
            [
                'name' => 'admin',
                'group_id' => 2,
            ],
            [
                'name' => 'test',
                'group_id' => 0,
            ],
        ]);

        wei()->db->batchInsert('test_user_groups', [
            [
                'id' => 1,
                'name' => 'vip',
            ],
            [
                'id' => 2,
                'name' => 'vip',
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
                'editor_id' => 2,
                'title' => 'Article 1',
                'content' => 'Content 1',
            ],
            [
                'test_user_id' => 2,
                'editor_id' => 1,
                'title' => 'Article 2',
                'content' => 'Content 2',
            ],
            [
                'test_user_id' => 1,
                'editor_id' => 2,
                'title' => 'Article 3',
                'content' => 'Content 3',
            ],
            [
                'test_user_id' => 3,
                'editor_id' => 2,
                'title' => 'Article 4',
                'content' => 'Content 4',
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
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` IN (?, ?, ?)', $queries[1]);
        $this->assertCount(2, $queries);

        $array = $articles->toArray();
        $this->assertArrayHasKey('user', $array[0]);
        $this->assertEquals(1, $array[0]['user']['id']);
    }

    public function testCollBelongsToLazyLoad()
    {
        $articles = TestArticle::new();

        $articles->limit(2)->all();

        foreach ($articles as $article) {
            $user = $article->user;
            $this->assertEquals($article->test_user_id, $user->id);
        }

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles` LIMIT 2', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` = ? LIMIT 1', $queries[2]);
        $this->assertCount(3, $queries);

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

        $articles->findAll([1, 2, 3])->load('tags');

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
        $this->assertEquals('SELECT * FROM `test_articles` WHERE `id` IN (?, ?, ?)', $queries[0]);
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

        $articles->findAll([1, 2, 3])->load('customTags');

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
        $this->assertEquals('SELECT * FROM `test_articles` WHERE `id` IN (?, ?, ?)', $queries[0]);
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

    public function testLoadBelongsToHasOne()
    {
        $articles = TestArticle::new();

        $articles->findAll([1, 2, 3])->load('user.profile');

        $this->assertEquals(1, $articles[0]->id);
        $this->assertEquals(1, $articles[0]->user->id);
        $this->assertEquals(1, $articles[0]->user->profile->id);

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles` WHERE `id` IN (?, ?, ?)', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` IN (?, ?)', $queries[1]);
        $this->assertEquals('SELECT * FROM `test_profiles` WHERE `test_user_id` IN (?, ?)', $queries[2]);
        $this->assertCount(3, $queries);

        $array = $articles->toArray();
        $this->assertArrayHasKey('user', $array[0]);
        $this->assertArrayHasKey('profile', $array[0]['user']);
        $this->assertEquals(1, $array[0]['user']['profile']['id']);
    }

    public function testLoadBelongsToBelongsTo()
    {
        $articles = TestArticle::new();

        $articles->findAll([1, 2, 4])->load('user.group');

        $this->assertSame(1, $articles[0]->id);
        $this->assertSame(1, $articles[0]->user->id);
        $this->assertSame(1, $articles[0]->user->group->id);
        $this->assertNull($articles[2]->user->group);

        $queries = wei()->db->getQueries();
        $this->assertSame('SELECT * FROM `test_articles` WHERE `id` IN (?, ?, ?)', $queries[0]);
        $this->assertSame('SELECT * FROM `test_users` WHERE `id` IN (?, ?, ?)', $queries[1]);
        $this->assertSame('SELECT * FROM `test_user_groups` WHERE `id` IN (?, ?)', $queries[2]);
        $this->assertCount(3, $queries);

        $array = $articles->toArray();
        $this->assertArrayHasKey('user', $array[0]);
        $this->assertArrayHasKey('group', $array[0]['user']);
        $this->assertSame(1, $array[0]['user']['group']['id']);
    }

    public function testLoadHasManyBelongsTo()
    {
        $users = TestUser::new();

        $users->findAll([1, 2])->load('articles.editor');

        $this->assertSame(1, $users[0]->id);
        $this->assertSame(1, $users[0]->articles[0]->id);
        $this->assertSame(2, $users[0]->articles[0]->editor->id);

        $queries = $this->db->getQueries();

        $this->assertSame('SELECT * FROM `test_users` WHERE `id` IN (?, ?)', $queries[0]);
        $this->assertSame('SELECT * FROM `test_articles` WHERE `test_user_id` IN (?, ?)', $queries[1]);
        $this->assertSame('SELECT * FROM `test_users` WHERE `id` IN (?, ?)', $queries[2]);
        $this->assertCount(3, $queries);

        $array = $users->toArray();
        $this->assertArrayHasKey('articles', $array[0]);
        $this->assertArrayHasKey('editor', $array[0]['articles'][0]);
        $this->assertSame(2, $array[0]['articles'][0]['editor']['id']);
    }

    public function testLoadHasManyBelongsToMany()
    {
        $users = TestUser::new();

        $users->findAll([1, 2])->load('articles.tags');

        $this->assertSame(1, $users[0]->id);
        $this->assertSame(1, $users[0]->articles[0]->id);
        $this->assertSame(1, $users[0]->articles[0]->tags[0]->id);

        $queries = $this->db->getQueries();

        $this->assertSame('SELECT * FROM `test_users` WHERE `id` IN (?, ?)', $queries[0]);
        $this->assertSame('SELECT * FROM `test_articles` WHERE `test_user_id` IN (?, ?)', $queries[1]);
        $this->assertSame(implode(' ', [
            'SELECT `test_tags`.*, `test_articles_test_tags`.`test_article_id`',
            'FROM `test_tags`',
            'INNER JOIN `test_articles_test_tags` ON `test_articles_test_tags`.`test_tag_id` = `test_tags`.`id`',
            'WHERE `test_articles_test_tags`.`test_article_id` IN (?, ?, ?)',
        ]), $queries[2]);
        $this->assertCount(3, $queries);

        $array = $users->toArray();
        $this->assertArrayHasKey('articles', $array[0]);
        $this->assertArrayHasKey('tags', $array[0]['articles'][0]);
        $this->assertSame(1, $array[0]['articles'][0]['tags'][0]['id']);
    }

    public function testLoadCache()
    {
        /** @var TestArticle|TestArticle[] $articles */
        $articles = TestArticle::new();

        $articles->all()->load('user')->load('user');

        $queries = wei()->db->getQueries();
        $this->assertEquals('SELECT * FROM `test_articles`', $queries[0]);
        $this->assertEquals('SELECT * FROM `test_users` WHERE `id` IN (?, ?, ?)', $queries[1]);
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

    /**
     * @group saveRelation
     */
    public function testSaveRelationWithObject()
    {
        $user = TestUser::save();

        /** @var TestArticle|TestArticle[] $articles */
        $articles = $user->articles()->saveRelation([
            (object) [
                'title' => 'title 1',
            ],
            (object) [
                'title' => 'title 2',
            ],
        ]);

        $this->assertCount(2, $articles);
        $this->assertSame($user->id, $articles[0]->test_user_id);
    }

    public function testModelLoadWillThrowsException()
    {
        $user = TestUser::save();

        $this->expectExceptionObject(
            new \BadMethodCallException('Method "load" can be called when the object is a collection')
        );

        $user->load('profile');
    }

    public function testGetSelfMethodAsRelation()
    {
        $user = TestUser::new();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessageMatches(
            '/Property or object "getEventResult" \(class "Wei\\\GetEventResult"\) not found, called in file/'
        );

        // @phpstan-ignore-next-line
        $user->getEventResult;
    }

    public function testGetParentModelMethodAsRelation()
    {
        $user = TestUser::new();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessageMatches(
            '/Property or object "getGuarded" \(class "Wei\\\GetGuarded"\) not found, called in file/'
        );

        // @phpstan-ignore-next-line
        $user->getGuarded;
    }

    public function testGetModelMethodHasArgumentAsRelation()
    {
        $user = TestUser::new();

        $this->expectException(\ArgumentCountError::class);
        $this->expectExceptionMessageMatches(implode(' ', [
            '/Too few arguments to function',
            'MiaoxingTest\\\Plugin\\\Model\\\Fixture\\\TestUser::methodHasArg\(\), 0 passed/',
        ]));

        // @phpstan-ignore-next-line
        $user->methodHasArg;
    }

    public function testIsRelation()
    {
        $user = TestUser::new();

        $this->assertTrue($user->isRelation('group'));
        $this->assertFalse($user->isRelation('articles'));
    }

    public function testInstanceRelationModelByModel()
    {
        $user = TestUser::new();
        $profile = TestProfile::new();

        $model = $user->hasOne($profile);
        $this->assertInstanceOf(TestProfile::class, $model);
        $this->assertSame($model, $profile);
    }

    public function testInstanceRelationModelByClass()
    {
        $user = TestUser::new();
        $model = $user->hasOne(TestProfile::class);
        $this->assertInstanceOf(TestProfile::class, $model);
    }

    public function testInstanceRelationModelByInvalidName()
    {
        $user = TestUser::new();

        $this->expectExceptionObject(
            new \InvalidArgumentException(implode(' ', [
                'Expected "model" argument to be a subclass or an instance of WeiBaseModel,',
                '"abc" given',
            ]))
        );

        $user->hasOne('abc');
    }

    public function testSameRelationClass()
    {
        $article = TestArticle::find(1);

        $user = $article->user;
        $this->assertInstanceOf(TestUser::class, $user);
        $this->assertSame(1, $user->id);

        $editor = $article->editor;
        $this->assertInstanceOf(TestUser::class, $editor);
        $this->assertSame(2, $editor->id);
    }

    public function testJoinRelation()
    {
        $user = TestUser::new()->joinRelation('profile');
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_users`',
            'INNER JOIN `test_profiles` ON `test_profiles`.`test_user_id` = `test_users`.`id`',
        ]), $user->getSql());
    }

    public function testInnerJoinRelation()
    {
        $user = TestUser::new()->innerJoinRelation('profile');
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_users`',
            'INNER JOIN `test_profiles` ON `test_profiles`.`test_user_id` = `test_users`.`id`',
        ]), $user->getSql());
    }

    public function testLeftJoinRelation()
    {
        $user = TestUser::new()->leftJoinRelation('profile');
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_users`',
            'LEFT JOIN `test_profiles` ON `test_profiles`.`test_user_id` = `test_users`.`id`',
        ]), $user->getSql());
    }

    public function testRightJoinRelation()
    {
        $user = TestUser::new()->rightJoinRelation('profile');
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_users`',
            'RIGHT JOIN `test_profiles` ON `test_profiles`.`test_user_id` = `test_users`.`id`',
        ]), $user->getSql());
    }

    public function testJoinRelations()
    {
        $user = TestUser::new()->joinRelation(['profile', 'group']);
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_users`',
            'INNER JOIN `test_profiles` ON `test_profiles`.`test_user_id` = `test_users`.`id`',
            'INNER JOIN `test_user_groups` ON `test_user_groups`.`id` = `test_users`.`group_id`',
        ]), $user->getSql());
    }

    public function testJoinRelationCache()
    {
        $user = TestUser::new()->joinRelation('profile')->joinRelation('profile');
        $this->assertSame(implode(' ', [
            'SELECT * FROM `test_users`',
            'INNER JOIN `test_profiles` ON `test_profiles`.`test_user_id` = `test_users`.`id`',
        ]), $user->getSql());
    }

    protected function clearLogs()
    {
        // preload fields cache
        TestUser::getColumns();
        TestUserGroup::getColumns();
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
            ->int('editor_id')
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
