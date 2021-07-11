<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\Test\BaseTestCase;
use Wei\Req;
use Wei\Res;

/**
 * @mixin \ViewMixin
 */
class RetTest extends BaseTestCase
{
    public function testHtmlToRes()
    {
        $req = new Req([
            'wei' => $this->wei,
            'fromGlobal' => false,
            'pathInfo' => '/m/test',
        ]);
        $res = new Res(['wei' => $this->wei]);

        // TODO page 服务移到 plugin 中
        $origDirs = $this->view->getOption('dirs');
        $this->view->setDirs([dirname(__DIR__) . '/Fixture/views']);

        $origDefaultLayout = $this->view->getOption('defaultLayout');
        $this->view->setDefaultLayout('layout.php');

        $ret = err('err', 1);
        $ret->toRes($req, $res);

        $this->assertStringContainsString('<h2 class="ret-title">err</h2>', $res->getContent());

        $this->view->setDirs($origDirs);
        $this->view->setDefaultLayout($origDefaultLayout);
    }

    public function testApiToRes()
    {
        $req = new Req([
            'wei' => $this->wei,
            'fromGlobal' => false,
            'pathInfo' => '/m-api/test',
        ]);
        $res = new Res(['wei' => $this->wei]);

        $ret = err('err', 1);
        $ret->toRes($req, $res);

        $this->assertSame('{"message":"err","code":1}', $res->getContent());
    }

    public function testTransform()
    {
        $ret = suc(['data' => ['id' => 1, 'password' => 2]]);

        $ret->transform(new class () extends BaseService {
            /**
             * @svc
             * @param mixed $data
             */
            protected function toArray($data): array
            {
                return [
                    'data' => [
                        'id' => $data['id'],
                    ],
                ];
            }
        });

        $this->assertSame(['id' => 1], $ret['data']);
    }

    public function testTransformWithoutData()
    {
        $ret = err('err', 1);

        $ret->transform(new class () extends BaseService {
            /**
             * @svc
             */
            protected function toArray()
            {
                throw new \Exception('should not called');
            }
        });

        $this->assertArrayNotHasKey('data', $ret);
    }

    public function testTransformWithInvalidArgument()
    {
        $ret = suc(['data' => ['id' => 1, 'password' => 2]]);

        $this->expectExceptionObject(new \InvalidArgumentException(
            'Expected class `stdClass` to have method `toArray`'
        ));

        $ret->transform(\stdClass::class);
    }
}
