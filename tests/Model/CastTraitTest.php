<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Test\BaseTestCase;

class CastTraitTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::dropTables();
        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Plugin\Fixture');

        wei()->schema->table('test_casts')
            ->id('int_column')
            ->bool('bool_column')
            ->string('string_column')
            ->datetime('datetime_column')
            ->date('date_column')
            ->string('json_column')
            ->exec();

        wei()->db->batchInsert('test_casts', [
            [
                'int_column' => 1,
                'bool_column' => false,
                'string_column' => '1',
                'datetime_column' => '2018-01-01 00:00:00',
                'date_column' => '2018-01-01',
                'json_column' => '{"a":"b\\\\c","d":"中文"}',
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
                ],
                [
                    'intColumn' => 2,
                    'boolColumn' => true,
                    'stringColumn' => 'string',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
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
                ],
                [
                    'intColumn' => 3,
                    'boolColumn' => false,
                    'stringColumn' => '1',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
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
                ],
                [
                    'intColumn' => 4,
                    'boolColumn' => true,
                    'stringColumn' => '1',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => 'abc',
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
        $record = wei()->testCast();

        $record->fromArray($from);

        // data中的数据不变
        $data = $record->getData();
        foreach ($from as $key => $value) {
            $this->assertSame($value, $data[wei()->str->snake($key)]);
        }

        // 重新加载,数据会改变
        $record->save();
        $record = wei()->testCast()->findById($record->intColumn);
        foreach ($result as $key => $value) {
            $this->assertSame($value, $record->$key);
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
                ],
                [
                    'intColumn' => 1,
                    'boolColumn' => false,
                    'stringColumn' => '1',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
                ],
            ],
            [
                [
                    'intColumn' => 'abc',
                    'boolColumn' => '2',
                    'stringColumn' => 1,
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => '{"a":"b\\c","d":"中文"}', // 不能被json_decode,会变成null
                ],
                [
                    'intColumn' => 0,
                    'boolColumn' => true,
                    'stringColumn' => '1',
                    'datetimeColumn' => '2018-01-01 00:00:00',
                    'dateColumn' => '2018-01-01',
                    'jsonColumn' => null,
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
        $record = wei()->testCast();

        $record->fromArray($from);

        foreach ($result as $key => $value) {
            $this->assertSame($value, $record->$key);
        }
    }

    public function testFind()
    {
        $record = wei()->testCast()->findById(1);

        $this->assertSame(1, $record->intColumn);
        $this->assertSame(false, $record->boolColumn);
        $this->assertSame('1', $record->stringColumn);
        $this->assertSame('2018-01-01 00:00:00', $record->datetimeColumn);
        $this->assertSame('2018-01-01', $record->dateColumn);
        $this->assertSame(['a' => 'b\c', 'd' => '中文'], $record->jsonColumn);
    }

    public function testSave()
    {
        wei()->testCast()->save([
            'intColumn' => '5',
            'boolColumn' => '0',
            'stringColumn' => 1,
            'datetimeColumn' => '2018-01-01 00:00:00',
            'dateColumn' => '2018-01-01',
            'jsonColumn' => ['a' => 'b\c', 'd' => '中文'],
        ]);

        $data = wei()->db->select('test_casts', ['int_column' => 5]);

        $this->assertSame('5', $data['int_column']);
        $this->assertSame('0', $data['bool_column']);
        $this->assertSame('1', $data['string_column']);
        $this->assertSame('2018-01-01 00:00:00', $data['datetime_column']);
        $this->assertSame('2018-01-01', $data['date_column']);
        $this->assertSame('{"a":"b\\\\c","d":"中文"}', $data['json_column']);
    }

    public function testGetNewModel()
    {
        $cast = wei()->testCast();

        $this->assertSame([], $cast->jsonColumn);
    }
}
