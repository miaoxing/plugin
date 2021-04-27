<?php

namespace Service;

use Miaoxing\Plugin\Service\ObjectReq;
use Miaoxing\Plugin\Test\BaseTestCase;

class ObjectReqTest extends BaseTestCase
{
    public function testGet()
    {
        $req = new ObjectReq([
            'wei' => $this->wei,
            'fromGlobal' => false,
            'content' => json_encode([
                'a' => [
                    'b' => 'c',
                ],
            ]),
            'servers' => [
                'HTTP_CONTENT_TYPE' => 'application/json',
            ],
        ]);

        $this->assertInstanceOf(\stdClass::class, $req['a']);
        $this->assertSame('c', $req['a']->b);
    }
}
