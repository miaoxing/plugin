<?php

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestCast;

/**
 * @internal
 */
final class CastTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::dropTables();

        wei()->schema->table('test_casts')
            ->id('int_column')
            ->bool('bool_column')
            ->string('string_column')
            ->datetime('datetime_column')
            ->date('date_column')
            ->string('json_column')
            ->string('list_column')
            ->string('list2_column')
            ->exec();

        wei()->db->batchInsert('test_casts', [
            [
                'int_column' => 1,
                'bool_column' => false,
                'string_column' => '1',
                'datetime_column' => '2018-01-01 00:00:00',
                'date_column' => '2018-01-01',
                'json_column' => '{"a":"b\\\\c","d":"中文"}',
                'list_column' => 'a,b,c',
                'list2_column' => '1|2|3',
            ],
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        static::dropTables();
        parent::tearDownAfterClass();
    }

    public static function dropTables()
    {
        wei()->schema->dropIfExists('test_casts');
    }

    public static function providerForSet()
    {
        return [
            [
                [
                    'intColumn' => 2,
                    'boolColumn' => true,
                    'stringColumn' => 'string',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
                    'listColumn' => ['a', 'b'],
                    'list2Column' => [1, 2],
                ],
                [
                    'intColumn' => 2,
                    'boolColumn' => true,
                    'stringColumn' => 'string',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
                    'list2Column' => [1, 2],
                ],
            ],
            [
                [
                    'intColumn' => '3',
                    'boolColumn' => '0',
                    'stringColumn' => 1,
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
                    'list2Column' => [1, 1],
                ],
                [
                    'intColumn' => 3,
                    'boolColumn' => false,
                    'stringColumn' => '1',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
                    'list2Column' => [1, 1],
                ],
            ],
            [
                [
                    'intColumn' => '4.1',
                    'boolColumn' => 'bool',
                    'stringColumn' => true,
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => 'abc',
                    'listColumn' => 'abc',
                    'list2Column' => '123',
                ],
                [
                    'intColumn' => 4,
                    'boolColumn' => true,
                    'stringColumn' => '1',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => ['abc'],
                    'listColumn' => ['abc'],
                    'list2Column' => [123],
                ],
            ],
        ];
    }

    /**
     * 测试Set后的结果
     *
     * @param array $from
     * @param array $result
     * @dataProvider providerForSet
     */
    public function testSetAsDbType($from, $result)
    {
        $record = TestCast::new();

        $record->fromArray($from);

        // data中的数据不变
        $data = $record->getData();
        foreach ($from as $key => $value) {
            $this->assertSame($value, $data[wei()->str->snake($key)]);
        }

        // 重新加载,数据会改变
        $record->save();
        $record = TestCast::find((int) $record->intColumn);
        foreach ($result as $key => $value) {
            $this->assertSame($value, $record->{$key});
        }
    }

    public static function providerForGetAsPhpType()
    {
        return [
            [
                [
                    'intColumn' => '1',
                    'boolColumn' => '0',
                    'stringColumn' => 1,
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
                    'listColumn' => ['a', 'b'],
                    'list2Column' => [1, 2],
                ],
                [
                    'intColumn' => 1,
                    'boolColumn' => false,
                    'stringColumn' => '1',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
                    'listColumn' => ['a', 'b'],
                    'list2Column' => [1, 2],
                ],
            ],
            [
                [
                    'intColumn' => 'abc',
                    'boolColumn' => '2',
                    'stringColumn' => 1,
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => '{"a":"b\\c","d":"中文"}',
                    'listColumn' => 'a|b',
                    'list2Column' => '1,2',
                ],
                [
                    'intColumn' => 0,
                    'boolColumn' => true,
                    'stringColumn' => '1',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => [0 => '{"a":"b\c","d":"中文"}'],
                    'listColumn' => ['a|b'],
                    'list2Column' => [1],
                ],
            ],
            [
                [
                    'listColumn' => 'a,b',
                    'list2Column' => '1|2',
                ],
                [
                    'listColumn' => ['a', 'b'],
                    'list2Column' => [1, 2],
                ],
            ],
        ];
    }

    /**
     * 测试Get后的结果
     *
     * @param array $from
     * @param array $result
     * @dataProvider providerForGetAsPhpType
     */
    public function testGetAsPhpType($from, $result)
    {
        $record = TestCast::new();

        $record->fromArray($from);

        foreach ($result as $key => $value) {
            $this->assertSame($value, $record->{$key});
        }
    }

    public function testFind()
    {
        $record = TestCast::find(1);

        $this->assertSame(1, $record->intColumn);
        $this->assertFalse($record->boolColumn);
        $this->assertSame('1', $record->stringColumn);
        $this->assertSame('2018-01-01 00:00:00', $record->datetimeColumn);
        $this->assertSame('2018-01-01', $record->dateColumn);
        $this->assertSame(['a' => 'b\c', 'd' => '中文'], $record->jsonColumn);
        $this->assertSame(['a', 'b', 'c'], $record->listColumn);
        $this->assertSame([1, 2, 3], $record->list2Column);
    }

    public function testSave()
    {
        TestCast::save([
            'intColumn' => '5',
            'boolColumn' => '0',
            'stringColumn' => 1,
            'datetimeColumn' => '2018-01-01 00:00:00',
            'dateColumn' => '2018-01-01',
            'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
            'listColumn' => ['a', 'b', 'c'],
            'list2Column' => [1, 2, 3],
        ]);

        $data = wei()->db->select('test_casts', ['int_column' => 5]);

        $this->assertSame('5', $data['int_column']);
        $this->assertSame('0', $data['bool_column']);
        $this->assertSame('1', $data['string_column']);
        $this->assertSame('2018-01-01 00:00:00', $data['datetime_column']);
        $this->assertSame('2018-01-01', $data['date_column']);
        $this->assertSame('{"a":"b\\\\c","d":"中文"}', $data['json_column']);
        $this->assertSame('a,b,c', $data['list_column']);
        $this->assertSame('1|2|3', $data['list2_column']);
    }

    public function testGetNewModel()
    {
        $cast = TestCast::new();

        $this->assertSame([], $cast->jsonColumn);
    }

    public function testIncr()
    {
        $cast = TestCast::save([
            'stringColumn' => 6,
        ]);

        $cast->incr('string_column')->save();

        $cast->reload();

        $this->assertEquals(7, $cast->stringColumn);
    }

    public function testReloadJson()
    {
        $cast = TestCast::save([
            'json_column' => [
                'a' => 'b',
            ],
        ]);
        $this->assertEquals(['a' => 'b'], $cast->jsonColumn);

        $cast->reload();
        $this->assertEquals(['a' => 'b'], $cast->jsonColumn);
    }

    public function testSetJsonNotArrayValue()
    {
        $cast = TestCast::save([
            'json_column' => null,
        ]);
        $this->assertEquals([], $cast->jsonColumn);

        $cast->reload();
        $this->assertEquals([], $cast->jsonColumn);
    }

    public function testBeforeSave()
    {
        TestCast::on('beforeSave', 'changeDataBeforeSave');

        $cast = TestCast::save([
            'json_column' => [
                '1',
                '2',
                '3',
            ],
        ]);

        $this->assertSame('3', $cast->stringColumn);
    }

    public function testConvertEmptyDateStringToNull()
    {
        $cast = TestCast::save([
            'dateColumn' => '',
            'datetimeColumn' => '',
        ]);
        $this->assertNull($cast->dateColumn);
        $this->assertNull($cast->datetimeColumn);
    }

    public function testSaveNullDate()
    {
        $cast = TestCast::save([
            'datetimeColumn' => null,
        ]);
        $this->assertNull($cast->datetimeColumn);
    }
}
