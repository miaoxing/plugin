<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\BasePlugin;
use Miaoxing\Plugin\Service\Plugin;
use Miaoxing\Plugin\Service\Ret;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Model\Fixture\DbTrait;
use MiaoxingTest\Plugin\Model\Fixture\TestUser;
use Wei\Env;
use Wei\Req;
use Wei\Res;

/**
 * @mixin \ViewMixin
 */
class RetTest extends BaseTestCase
{
    use DbTrait;

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
            'pathInfo' => '/api/test',
        ]);
        $res = new Res(['wei' => $this->wei]);

        $ret = err('err', 1);
        $ret->toRes($req, $res);

        $this->assertSame('{"message":"err","code":1}', $res->getContent());
    }

    public function testGenerateCode()
    {
        $plugin = $this->getServiceMock(Plugin::class, [
            'getOneById',
        ]);

        $pluginInstance = $this->getMockBuilder(BasePlugin::class)
            ->onlyMethods(['getCode'])
            ->getMock();

        $pluginInstance->expects($this->exactly(3))
            ->method('getCode')
            ->willReturn(999);

        $plugin->expects($this->exactly(2))
            ->method('getOneById')
            ->willReturn($pluginInstance);

        $env = $this->getServiceMock(Env::class, ['isDev']);
        $env->expects($this->exactly(2))
            ->method('isDev')
            ->willReturn(true);

        $errorFile = __DIR__ . '/test_errors.php';
        $ret = $this->getServiceMock(Ret::class, ['getErrorFile', 'getPluginFromFile']);
        $ret->expects($this->exactly(3))
            ->method('getErrorFile')
            ->willReturn($errorFile);

        $ret->expects($this->exactly(2))
            ->method('getPluginFromFile')
            ->willReturn('plugin');

        $ret->setOption('plugin', $plugin);

        $message = 'test' . time();
        $ret->err($message);
        $this->assertSame(999001, $ret->getCode());

        $ret->err($message);
        $this->assertSame(999001, $ret->getCode());

        unlink($errorFile);
    }

    public function testToRetToRes()
    {
        $this->initFixtures();

        $req = new Req([
            'wei' => $this->wei,
            'fromGlobal' => false,
            'servers' => [
                'HTTP_ACCEPT' => 'application/json',
            ],
        ]);
        $res = new Res(['wei' => $this->wei]);

        $user = TestUser::first();
        /** @var Ret $ret */
        $ret = $user->toRet();
        $ret->toRes($req, $res);
        $this->assertSame(200, $res->getStatusCode());

        $user = TestUser::save();
        /** @var Ret $ret */
        $ret = $user->toRet();
        $ret->toRes($req, $res);
        $this->assertSame(201, $res->getStatusCode());
    }
}
