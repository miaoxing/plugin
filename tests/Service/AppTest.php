<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\App;
use Miaoxing\Plugin\Service\AppModel;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\Controller\TestController;

class AppTest extends BaseTestCase
{
    /**
     * 测试返回数据
     *
     * @dataProvider dataForResponse
     * @param $action
     * @param $content
     */
    public function testResponse($action, $content)
    {
        $app = wei()->app;
        $app->setControllerMap(['test' => TestController::class]);

        $app->request->set('_format', 'json');

        // 更改视图为测试的目录
        $origDirs = $app->view->getOption('dirs');
        $app->view->setDirs([dirname(__DIR__) . '/Fixture/views']);

        ob_start();
        $app->dispatch('test', $action);
        $response = ob_get_clean();

        // 还原视图目录
        $app->view->setDirs($origDirs);

        $this->assertStringContainsString($content, $response);
    }

    public function dataForResponse()
    {
        return [
            [
                'suc',
                json_encode(wei()->ret->suc(), JSON_UNESCAPED_UNICODE),
            ],
            [
                'err',
                '{"message":"err","code":-2}',
            ],
            [
                'returnCodeAndMessage',
                '{"code":1,"message":"returnCodeAndMessage"}',
            ],
            [
                'returnOnlyCode',
                'returnOnlyCode',
            ],
            [
                'returnOnlyMessage',
                'returnOnlyMessage',
            ],
            [
                'returnResponse',
                'returnResponse',
            ],
            [
                'returnEmptyArrayWillRenderView',
                'returnEmptyArrayWillRenderView',
            ],
            [
                'returnRetInMiddleware',
                '{"message":"returnRetInMiddleware","code":1}',
            ],
            [
                'returnStringInMiddleware',
                'returnStringInMiddleware',
            ],
            [
                'returnResponseInMiddleware',
                'returnResponseInMiddleware',
            ],
        ];
    }

    public function testGetIdByDomain()
    {
        $prefix = 'appDomain:';
        $app = AppModel::findOrInitBy(['domain' => 't.test.com']);

        $app->save([
            'name' => 'domain',
            'domain' => '',
        ]);

        wei()->cache->remove($prefix . 't.test.com');
        $this->assertFalse(App::getIdByDomain('t.test.com'));

        wei()->cache->remove($prefix . 't.test.com');
        $app->save(['domain' => 't.test.com']);

        $this->assertEquals('domain', App::getIdByDomain('t.test.com'));

        $app->destroy();
    }
}
