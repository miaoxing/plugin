<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Cls;
use Miaoxing\Plugin\Test\BaseTestCase;
use stdClass;

class ClsTest extends BaseTestCase
{
    /**
     * @param string $expected
     * @param string $actual
     * @return void
     * @dataProvider providerForBaseName
     */
    public function testBaseName(string $expected, string $actual)
    {
        $this->assertSame($expected, $actual);
    }

    public function providerForBaseName(): array
    {
        return [
            ['ClsTest', Cls::baseName(static::class)],
            ['test', Cls::baseName('test')],
            [stdClass::class, Cls::baseName(stdClass::class)],
        ];
    }
}