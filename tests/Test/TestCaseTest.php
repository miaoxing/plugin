<?php

namespace MiaoxingTest\Plugin\Test;

use Miaoxing\Plugin\Test\BaseTestCase;
use Wei\Ret;

class TestCaseTest extends BaseTestCase
{
    public function testSameRet()
    {
        $this->assertSameRet(suc(), suc());
        $this->assertSameRet(err('test', 1), err('test', 1));
    }

    /**
     * @dataProvider providerForRetFail
     * @param Ret $expected
     * @param Ret $actual
     */
    public function testSameRetFail(Ret $expected, Ret $actual): void
    {
        $message = '';
        try {
            $this->assertSameRet($expected, $actual);
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }
        $this->assertSame('Failed asserting that two arrays are identical.', $message);
    }

    public static function providerForRetFail(): array
    {
        return [
            [
                suc('a'),
                suc('b'),
            ],
            [
                err('a', 1),
                err('a', 2),
            ],
        ];
    }
}
