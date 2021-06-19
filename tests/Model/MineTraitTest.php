<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Service\User;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestMine;

class MineTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::dropTables();

        wei()->schema->table('test_mines')
            ->id()
            ->int('user_id')
            ->exec();

        TestMine::save(['user_id' => 1]);
    }

    public static function tearDownAfterClass(): void
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_mines');
    }

    public function testMine()
    {
        User::loginById(1);

        $mine = TestMine::find(1);
        $mine2 = TestMine::mine()->first();

        $this->assertSame($mine->id, $mine2->id);
    }

    public function testFindOrInitMine()
    {
        User::loginById(1);

        $mine = TestMine::find(1);
        $mine2 = TestMine::findOrInitMine();

        $this->assertSame($mine->id, $mine2->id);
    }

    public function testFindOrInitMineAndSave()
    {
        User::loginById(1);

        TestMine::mine()->all()->destroy();

        $mine = TestMine::findOrInitMine();
        $this->assertTrue($mine->isNew());

        $mine->save();
        $this->assertSame(1, $mine->get('user_id'));
    }

    public function testFindOrInitMineCached()
    {
        User::loginById(1);

        $mine = TestMine::findOrInitMineCached();
        $mine2 = TestMine::findOrInitMineCached();

        $this->assertSame($mine->id, $mine2->id);
        $this->assertSame($mine, $mine2);
    }
}
