<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

class CliTest extends BaseTestCase
{
    /**
     * 测试自定义样式
     *
     * @dataProvider  providerForUserStyles
     *
     * @param string $step
     * @param string $expect
     * @param string $actual
     */
    public function testUserStyles($step, $expect, $actual)
    {
        $this->step($step . $expect);
        $this->assertEquals($expect, $actual);
    }

    public function providerForUserStyles()
    {
        return [
            [
                'step' => '输出文本为绿色',
                'output' => "\033[0;32mY\x1b[0m",
                '' => wei()->cli->success('Y'),
            ],
            [
                'step' => '输出文本为红色',
                'output' => "\033[0;31mN\x1b[0m",
                '' => wei()->cli->error('N'),
            ],
        ];
    }
}
