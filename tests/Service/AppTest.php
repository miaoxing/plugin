<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\App;
use Miaoxing\Plugin\Service\AppModel;
use Miaoxing\Plugin\Service\User;
use Miaoxing\Plugin\Test\BaseTestCase;
use MiaoxingTest\Plugin\Fixture\Controller\TestController;

/**
 * @internal
 */
final class AppTest extends BaseTestCase
{
    public function testParamAction()
    {
        wei()->req->set('id', 'id');
        $response = $this->execute('param');
        $this->assertSame('id', $response);
    }

    public function testParamWithTypeAction()
    {
        wei()->req->set('id', '1');
        $response = $this->execute('paramWithType');
        $this->assertSame('integer-1', $response);
    }

    public function testParamWithDefaultValueAction()
    {
        $response = $this->execute('paramWithDefaultValue');
        $this->assertSame('test', $response);
    }

    public function testParamWithTypeAndDefaultValueAction()
    {
        $response = $this->execute('paramWithTypeAndDefaultValue');
        $this->assertSame('NULL', $response);

        wei()->req->set('isEnabled', '1');
        $response = $this->execute('paramWithTypeAndDefaultValue');
        $this->assertSame('true', $response);

        wei()->req->set('isEnabled', '0');
        $response = $this->execute('paramWithTypeAndDefaultValue');
        $this->assertSame('false', $response);
    }

    public function testParamRequiredAction()
    {
        $this->expectExceptionObject(new \Exception('Missing required parameter: id', 400));
        $this->execute('param');
    }

    public function testParamModelAction()
    {
        wei()->req->set('id', '1');
        $response = $this->execute('paramModel');
        $this->assertSame('user:1', $response);
    }

    /**
     * 测试返回数据
     *
     * @dataProvider dataForResponse
     * @param string $action
     * @param string $content
     */
    public function testResponse(string $action, string $content)
    {
        User::loginById(1);

        $app = wei()->app;
        $app->setControllerMap(['test' => TestController::class]);

        $app->req->set('_format', 'json');

        // 更改视图为测试的目录
        $origDirs = $app->view->getOption('dirs');
        $app->view->setDirs([dirname(__DIR__) . '/Fixture/views']);

        ob_start();
        $app->dispatch('test', $action);
        $response = ob_get_clean();

        // 还原视图目录
        $app->view->setDirs($origDirs);

        $this->assertSame($content, $response);
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

    protected function execute($action)
    {
        User::loginById(1);

        $app = wei()->app;
        $app->setControllerMap(['test' => TestController::class]);

        $app->req->set('_format', 'json');

        // 更改视图为测试的目录
        $origDirs = $app->view->getOption('dirs');
        $app->view->setDirs([dirname(__DIR__) . '/Fixture/views']);

        ob_start();
        try {
            $app->dispatch('test', $action);
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $response = ob_get_clean();

            // 还原视图目录
            $app->view->setDirs($origDirs);
        }

        return $response;
    }
}
