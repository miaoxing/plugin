<?php

declare(strict_types=1);

namespace MiaoxingTest\Plugin\Model;

use Miaoxing\Plugin\Service\QueryBuilder;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\TestCast;

/**
 * @internal
 * @phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps
 */
final class CastTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::dropTables();

        wei()->schema->table('test_casts')
            ->id('int_column')
            ->int('nullable_int_column')->nullable()
            ->int('nullable_default_int_column')->nullable()->defaults(7)
            ->bool('bool_column')
            ->bool('nullable_bool_column')->nullable()
            ->string('string_column')
            ->string('nullable_string_column')->nullable()
            ->datetime('datetime_column')->nullable(false)
            ->datetime('nullable_datetime_column')->nullable()
            ->date('date_column')->nullable(false)
            ->date('nullable_date_column')->nullable()
            ->string('json_column')
            ->string('nullable_json_column')->nullable()
            ->string('list_column')
            ->string('nullable_list_column')->nullable()
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
        $cast = TestCast::new();

        $cast::onModelEvent('beforeSave', function () use ($cast) {
            // @phpstan-ignore-next-line cast to string
            $cast->string_column = count($cast->json_column);
        });

        $cast->save([
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

    public function testSetNull()
    {
        $cast = TestCast::new();
        foreach ($cast->getColumns() as $name => $column) {
            $cast->set($name, null);
        }
        $cast->save();

        $data = $this->wei->db->select($cast->getTable(), ['int_column' => $cast->int_column]);
        $this->assertSame([
            'int_column' => (string) $cast->int_column,
            'nullable_int_column' => null,
            'nullable_default_int_column' => null,
            'bool_column' => '0',
            'nullable_bool_column' => null,
            'string_column' => '',
            'nullable_string_column' => null,
            'datetime_column' => null,
            'nullable_datetime_column' => null,
            'date_column' => null,
            'nullable_date_column' => null,
            'json_column' => '[]',
            'nullable_json_column' => null,
            'list_column' => '',
            'nullable_list_column' => null,
            'list2_column' => '',
        ], $data);
    }

    public function testSetDateNull()
    {
        $cast = TestCast::new();
        $cast->date_column = null;
        $this->assertNull($cast->date_column);
    }

    public function testSetListEmptyValue()
    {
        $cast = TestCast::new();

        // @phpstan-ignore-next-line cast to []
        $cast->list_column = '';
        $this->assertSame([], $cast->list_column);

        // @phpstan-ignore-next-line cast to []
        $cast->list_column = null;
        // @phpstan-ignore-next-line
        $this->assertSame([], $cast->list_column);

        $cast->list_column = [];
        $this->assertSame([], $cast->list_column);
    }

    public function testGetColumnCasts()
    {
        $casts = TestCast::new()->getColumnCasts();

        $this->assertSame([
            'int_column' => 'int',
            'nullable_int_column' => 'int',
            'nullable_default_int_column' => 'int',
            'bool_column' => 'bool',
            'nullable_bool_column' => 'bool',
            'string_column' => 'string',
            'nullable_string_column' => 'string',
            'datetime_column' => 'datetime',
            'nullable_datetime_column' => 'datetime',
            'date_column' => 'date',
            'nullable_date_column' => 'date',
            'json_column' => 'array',
            'nullable_json_column' => 'string',
            'list_column' => 'list',
            'nullable_list_column' => 'string',
            'list2_column' => [
                'list',
                'type' => 'int',
                'separator' => '|',
            ],
        ], $casts);
    }

    /**
     * @group change
     */
    public function testUpdate()
    {
        $cast = TestCast::save();

        $cast->json_column = ['a' => 'b'];
        $cast->save();

        // Save string to database
        $data = wei()->db->select($cast->getTable(), ['int_column' => $cast->int_column]);
        $this->assertSame('{"a":"b"}', $data['json_column']);

        // After save, convert back to array
        $this->assertSame(['a' => 'b'], $cast->json_column);
    }

    public function testDefaultToArray()
    {
        $array = TestCast::toArray();
        $this->assertSame([
            'int_column' => null,
            'nullable_int_column' => null,
            'nullable_default_int_column' => 7,
            'bool_column' => false,
            'nullable_bool_column' => null,
            'string_column' => '',
            'nullable_string_column' => null,
            'datetime_column' => null,
            'nullable_datetime_column' => null,
            'date_column' => null,
            'nullable_date_column' => null,
            'json_column' => [],
            'nullable_json_column' => null,
            'list_column' => [],
            'nullable_list_column' => null,
            'list2_column' => [],
        ], $array);
    }

    public function testDefaultSave()
    {
        $cast = TestCast::save();

        $data = QueryBuilder::table($cast->getTable())->where('int_column', $cast->int_column)->first();

        $this->assertSame('7', $data['nullable_default_int_column']);
        $this->assertSame('[]', $data['json_column']);
    }

    public function testDefaultGet()
    {
        $cast = TestCast::new();
        $this->assertSame(7, $cast->nullable_default_int_column);
    }

    public function testDefaultSet()
    {
        $cast = TestCast::new();

        // @phpstan-ignore-next-line cast to int
        $cast->nullable_default_int_column = '7';
        $this->assertSame(7, $cast->nullable_default_int_column);
    }

    public function testDefaultOverwriteByNew()
    {
        $cast = TestCast::new();
        $this->assertSame(7, $cast->nullable_default_int_column);

        $cast = TestCast::new([
            'nullable_default_int_column' => 1,
        ]);
        $this->assertSame(1, $cast->nullable_default_int_column);
    }

    public function testDefaultUnset()
    {
        $cast = TestCast::new();

        $cast->nullable_int_column = 1;
        $cast->nullable_int_column = null;
        $this->assertNull($cast->nullable_int_column);

        $cast->nullable_default_int_column = 1;
        $cast->nullable_default_int_column = null;
        $this->assertNull($cast->nullable_default_int_column);
    }

    public function testDefaultSetNull()
    {
        $cast = TestCast::new();

        $cast->nullable_int_column = null;
        $this->assertNull($cast->nullable_int_column);

        $cast->nullable_default_int_column = null;
        $this->assertNull($cast->nullable_default_int_column);
    }

    public function testDefaultIsset()
    {
        $cast = TestCast::new();

        $this->assertFalse(isset($cast->nullable_int_column));
        $this->assertTrue(isset($cast->nullable_default_int_column));
    }
}
