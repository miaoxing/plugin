<?php

namespace Miaoxing\Plugin\Traits;

use Miaoxing\Plugin\Test\BaseTestCase;

class HasCastTest extends BaseTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::dropTables();
        wei()->import(dirname(__DIR__) . '/Fixture', 'MiaoxingTest\Plugin\Fixture');

        wei()->schema->table('test_has_casts')
            ->id('int_column')
            ->bool('bool_column')
            ->string('string_column')
            ->datetime('datetime_column')
            ->date('date_column')
            ->string('json_column')
            ->exec();

        wei()->db->insertBatch('test_has_casts', [
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
        wei()->schema->dropIfExists('test_has_casts');
    }

    public static function providerForSet()
    {
        return [
            [
                [
                    'int_column' => 1,
                    'bool_column' => true,
                    'string_column' => 'string',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['a' => 'b\c', 'd' => '中文'],
                ],
                [
                    'int_column' => 1,
                    'bool_column' => true,
                    'string_column' => 'string',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => '{"a":"b\\\\c","d":"中文"}',
                ]
            ],
            [
                [
                    'int_column' => '1',
                    'bool_column' => '0',
                    'string_column' => 1,
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['a' => 'b\c', 'd' => '中文'],
                ],
                [
                    'int_column' => 1,
                    'bool_column' => false,
                    'string_column' => '1',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => '{"a":"b\\\\c","d":"中文"}',
                ]
            ],
            [
                [
                    'int_column' => 'abc',
                    'bool_column' => 'bool',
                    'string_column' => true,
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => 'abc',
                ],
                [
                    'int_column' => 0,
                    'bool_column' => true,
                    'string_column' => '1',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => 'abc', // Ignore string
                ]
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
        $record = wei()->testHasCast();

        $record->fromArray($from);

        $data = $record->getData();

        foreach ($result as $key => $value) {
            $this->assertSame($value, $data[$key]);
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
                ],
                [
                    'int_column' => 1,
                    'bool_column' => false,
                    'string_column' => '1',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['a' => 'b\c', 'd' => '中文'],
                ]
            ],
            [
                [
                    'int_column' => 'abc',
                    'bool_column' => '2',
                    'string_column' => 1,
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => '{"a":"b\\\\c","d":"中文"}',
                ],
                [
                    'int_column' => 0,
                    'bool_column' => true,
                    'string_column' => '1',
                    'datetime_column' => '2018-01-01 00:00:00',
                    'date_column' => '2018-01-01',
                    'json_column' => ['a' => 'b\c', 'd' => '中文'],
                ]
            ]
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
        $record = wei()->testHasCast();

        $record->fromArray($from);

        foreach ($result as $key => $value) {
            $this->assertSame($value, $record[$key]);
        }
    }

    public function testFind()
    {
        $record = wei()->testHasCast()->findById(1);

        $this->assertSame(1, $record['int_column']);
        $this->assertSame(false, $record['bool_column']);
        $this->assertSame('1', $record['string_column']);
        $this->assertSame('2018-01-01 00:00:00', $record['datetime_column']);
        $this->assertSame('2018-01-01', $record['date_column']);
        $this->assertSame(['a' => 'b\c', 'd' => '中文'], $record['json_column']);
    }

    public function testSave()
    {
        wei()->testHasCast()->save([
            'int_column' => '2',
            'bool_column' => '0',
            'string_column' => 1,
            'datetime_column' => '2018-01-01 00:00:00',
            'date_column' => '2018-01-01',
            'json_column' => ['a' => 'b\c', 'd' => '中文'],
        ]);

        $data = wei()->db->select('test_has_casts', ['int_column' => 2]);

        $this->assertSame('2', $data['int_column']);
        $this->assertSame('0', $data['bool_column']);
        $this->assertSame('1', $data['string_column']);
        $this->assertSame('2018-01-01 00:00:00', $data['datetime_column']);
        $this->assertSame('2018-01-01', $data['date_column']);
        $this->assertSame('{"a":"b\\\\c","d":"中文"}', $data['json_column']);
    }
}
