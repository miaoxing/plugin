<?php

namespace MiaoxingTest\Plugin\Service;

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
}
