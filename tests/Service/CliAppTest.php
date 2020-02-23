<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;
use Miaoxing\Services\Service\CliApp;

/**
 * 命令行服务
 */
class CliAppTest extends BaseTestCase
{
    /**
     * 测试命令行参数能正确解析
     *
     * @param array $argv
     * @param array $args
     * @dataProvider providerForArgs
     */
    public function testArgs(array $argv, array $args)
    {
        wei()->request->setServer('argv', $argv);

        $cliApp = new CliApp([
           'wei' => $this->wei,
        ]);

        $data = $cliApp->request->toArray();
        $this->assertArraySubset($args, $data, true);
    }

    public function providerForArgs()
    {
        $data = [
            // Single option
            '-v' => [
                'v' => true,
            ],
            '--version' => [
                'version' => true,
            ],
            '-abc' => [
                'a' => true,
                'b' => true,
                'c' => true,
            ],
            '-b=c' => [
                'b' => 'c',
            ],
            '--option=value' => [
                'option' => 'value',
            ],
            '--option value' => [
                'option' => 'value',
            ],
            '--long-opt value' => [
                'long-opt' => 'value',
            ],
            // Multi options
            '-a -b -c' => [
                'a' => true,
                'b' => true,
                'c' => true,
            ],
            '-v --version' => [
                'v' => true,
                'version' => true,
            ],
            // Argument without name
            '-v from to' => [
                'v' => true,
                0 => 'from',
                1 => 'to',
            ],
            '-v from to -d' => [
                'v' => true,
                0 => 'from',
                1 => 'to',
                'd' => true,
            ],
            'from to -v' => [
                0 => 'from',
                1 => 'to',
                'v' => true,
            ],
            '-v --path=abc' => [
                'v' => true,
                'path' => 'abc',
            ],
            '-v --path abc' => [
                'v' => true,
                'path' => 'abc',
            ],
            // Overwrite
            '--dir=/foo --dir=/bar' => [
                'dir' => '/bar',
            ],
        ];

        $args = [];
        foreach ($data as $line => $options) {
            $args[] = [
                explode(' ', $line),
                $options,
            ];
        }

        return $args;
    }
}
