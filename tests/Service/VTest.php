<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

class VTest extends BaseTestCase
{
    public function testCheckFail()
    {
        $ret = wei()->v()
            ->key('question', '问题')
            ->check([]);

        $this->assertRetErr($ret, -1, '问题不能为空');
    }

    public function testCheckPass()
    {
        $ret = wei()->v()
            ->key('question', '问题')
            ->check([
                'question' => '问题'
            ]);

        $this->assertRetSuc($ret);
    }
}
