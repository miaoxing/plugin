<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

class StrTest extends BaseTestCase
{
    public function providerForSnake()
    {
        return [
            ['a', 'a'],
            ['abc', 'abc'],
            ['abC', 'ab_c'],
            ['abCd', 'ab_cd'],
            ['ab_cd', 'ab_cd'],
        ];
    }

    /**
     * @param string $input
     * @param string $output
     * @dataProvider providerForSnake
     */
    public function testSnake($input, $output)
    {
        $this->assertEquals($output, wei()->str->snake($input));
    }

    public function providerForDash()
    {
        return [
            ['a', 'a'],
            ['abc', 'abc'],
            ['abC', 'ab-c'],
            ['abCd', 'ab-cd'],
            ['ab-cd', 'ab-cd'],
        ];
    }

    /**
     * @param string $input
     * @param string $output
     * @dataProvider providerForDash
     */
    public function testDash($input, $output)
    {
        $this->assertEquals($output, wei()->str->dash($input));
    }
}
