<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\Tester;
use Miaoxing\Plugin\Test\BaseTestCase;

class TesterTest extends BaseTestCase
{
    public function testPost()
    {
        $tester = $this->getServiceMock(Tester::class, [
            'call',
        ]);

        $tester->expects($this->once())
            ->method('call')
            ->with('/test', 'post');

        $tester->post('/test');
    }
}
