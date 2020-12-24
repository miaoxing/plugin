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
                    'int_column' => 2,
                    'bool_column' => true,
                    'string_column' => 'string',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['a' => 'b\c', 'd' => '中文'],
                    'list_column' => ['a', 'b'],
                    'list2_column' => [1, 2],
                ],
                [
                    'int_column' => 2,
                    'bool_column' => true,
                    'string_column' => 'string',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['a' => 'b\c', 'd' => '中文'],
                    'list2_column' => [1, 2],
                ],
            ],
            [
                [
                    'int_column' => '3',
                    'bool_column' => '0',
                    'string_column' => 1,
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['a' => 'b\c', 'd' => '中文'],
                    'list2_column' => [1, 1],
                ],
                [
                    'int_column' => 3,
                    'bool_column' => false,
                    'string_column' => '1',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['a' => 'b\c', 'd' => '中文'],
                    'list2_column' => [1, 1],
                ],
            ],
            [
                [
                    'int_column' => '4.1',
                    'bool_column' => 'bool',
                    'string_column' => true,
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => 'abc',
                    'list_column' => 'abc',
                    'list2_column' => '123',
                ],
                [
                    'int_column' => 4,
                    'bool_column' => true,
                    'string_column' => '1',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['abc'],
                    'list_column' => ['abc'],
                    'list2_column' => [123],
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
        $data = $record->getAttributes();
        foreach ($from as $key => $value) {
            $this->assertSame($value, $data[$key]);
        }

        // 重新加载,数据会改变
        $record->save();
        $record = TestCast::find((int) $record->int_column);
        foreach ($result as $key => $value) {
            $this->assertSame($value, $record->{$key});
        }
    }

    public static function providerForGetAsPhpType()
    {
        return [
            [
                [
                    'int_column' => '1',
                    'bool_column' => '0',
                    'string_column' => 1,
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['a' => 'b\c', 'd' => '中文'],
                    'list_column' => ['a', 'b'],
                    'list2_column' => [1, 2],
                ],
                [
                    'int_column' => 1,
                    'bool_column' => false,
                    'string_column' => '1',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['a' => 'b\c', 'd' => '中文'],
                    'list_column' => ['a', 'b'],
                    'list2_column' => [1, 2],
                ],
            ],
            [
                [
                    'int_column' => 'abc',
                    'bool_column' => '2',
                    'string_column' => 1,
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => '{"a":"b\\c","d":"中文"}',
                    'list_column' => 'a|b',
                    'list2_column' => '1,2',
                ],
                [
                    'int_column' => 0,
                    'bool_column' => true,
                    'string_column' => '1',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => [0 => '{"a":"b\c","d":"中文"}'],
                    'list_column' => ['a|b'],
                    'list2_column' => [1],
                ],
            ],
            [
                [
                    'list_column' => 'a,b',
                    'list2_column' => '1|2',
                ],
                [
                    'list_column' => ['a', 'b'],
                    'list2_column' => [1, 2],
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

        $this->assertSame(1, $record->int_column);
        $this->assertFalse($record->bool_column);
        $this->assertSame('1', $record->string_column);
        $this->assertSame('2018-01-01 00:00:00', $record->datetime_column);
        $this->assertSame('2018-01-01', $record->date_column);
        $this->assertSame(['a' => 'b\c', 'd' => '中文'], $record->json_column);
        $this->assertSame(['a', 'b', 'c'], $record->list_column);
        $this->assertSame([1, 2, 3], $record->list2_column);
    }

    public function testSave()
    {
        TestCast::save([
            'int_column' => '5',
            'bool_column' => '0',
            'string_column' => 1,
            'datetime_column' => '2018-01-01 00:00:00',
            'date_column' => '2018-01-01',
            'json_column' => ['a' => 'b\c', 'd' => '中文'],
            'list_column' => ['a', 'b', 'c'],
            'list2_column' => [1, 2, 3],
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

        $this->assertSame([], $cast->json_column);
    }

    public function testIncr()
    {
        $cast = TestCast::save([
            'string_column' => 6,
        ]);

        $cast->incr('string_column')->save();

        $cast->reload();

        $this->assertEquals(7, $cast->string_column);
    }

    public function testReloadJson()
    {
        $cast = TestCast::save([
            'json_column' => [
                'a' => 'b',
            ],
        ]);
        $this->assertEquals(['a' => 'b'], $cast->json_column);

        $cast->reload();
        $this->assertEquals(['a' => 'b'], $cast->json_column);
    }

    public function testSetJsonNotArrayValue()
    {
        $cast = TestCast::save([
            'json_column' => null,
        ]);
        $this->assertEquals([], $cast->json_column);

        $cast->reload();
        $this->assertEquals([], $cast->json_column);
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

        $this->assertSame('3', $cast->string_column);
    }

    public function testConvertEmptyDateStringToNull()
    {
        $cast = TestCast::save([
            'date_column' => '',
            'datetime_column' => '',
        ]);
        $this->assertNull($cast->date_column);
        $this->assertNull($cast->datetime_column);
    }

    public function testSaveNullDate()
    {
        $cast = TestCast::save([
            'datetime_column' => null,
        ]);
        $this->assertNull($cast->datetime_column);
    }

    public function testGetColumnCasts()
    {
        $casts = TestCast::new()->getColumnCasts();
        $this->assertSame([
            'int_column' => 'int',
            'bool_column' => 'bool',
            'string_column' => 'string',
            'datetime_column' => 'datetime',
            'date_column' => 'date',
            'json_column' => 'array',
            'list_column' => 'list',
            'list2_column' => [
                0 => 'list',
                'type' => 'int',
                'separator' => '|',
            ],
        ], $casts);
    }
}
