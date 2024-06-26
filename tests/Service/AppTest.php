<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Service\AppModel;
use Miaoxing\Plugin\Service\User;
use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * @mixin \ReqPropMixin
 * @mixin \AppPropMixin
 * @internal
 */
final class AppTest extends BaseTestCase
{
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
        $app->req->set('_format', 'json');

        $test = require __DIR__ . '/../Fixture/pages/tests/index.php';

        // 更改视图为测试的目录
        $origDirs = $app->view->getOption('dirs');
        $app->view->setDirs([dirname(__DIR__) . '/Fixture/views']);

        ob_start();
        // @phpstan-ignore-next-line 待整理出直接调用的方法
        $app->execute($test, $action);
        $response = ob_get_clean();

        // 还原视图目录
        $app->view->setDirs($origDirs);

        $this->assertSame($content, $response);
    }

    public static function dataForResponse()
    {
        return [
            [
                'sucAction',
                json_encode(wei()->ret->suc(), \JSON_UNESCAPED_UNICODE),
            ],
            [
                'errAction',
                '{"message":"err","code":-2}',
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
                '{"message":"returnRetInMiddleware","code":0}',
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

    public function testGetId()
    {
        $app = wei()->app;
        $curId = $app->getId();

        $req = $app->req;
        $curDomain = $req->getHost();

        $prefix = 'appDomain:';

        $appModel = AppModel::findOrInitBy(['domain' => 't.test.com']);
        $appModel->save([
            'name' => 'domain',
            'domain' => '',
        ]);
        wei()->cache->delete($prefix . 't.test.com');

        $app->setId(null);
        $this->assertSame($curId, $app->getId());

        wei()->cache->delete($prefix . 't.test.com');
        $req->setServer('HTTP_HOST', 't.test.com');
        $appModel->save(['domain' => 't.test.com']);
        wei()->cache->delete($prefix . 't.test.com');

        // Clear id before get, other step may get id, cause id become 1
        $app->setId(null);
        $this->assertEquals($appModel->id, $app->getId());

        $app->setId($curId);
        $req->setServer('HTTP_HOST', $curDomain);
        $appModel->destroy();
    }

    public function testSetModelNull()
    {
        $app = wei()->app;
        $model = $app->getModel();

        $app->setModel(null);
        $model2 = $app->getModel();
        $this->assertNotSame($model, $model2);
    }

    public function testGetControllerAndAction()
    {
        $controller = __DIR__ . '/../Fixture/pages/rest/index.php';

        $res = $this->dispatch($controller, 'get');
        $this->assertSame($controller, $this->app->getController());
        $this->assertSame('get', $this->app->getAction());
        $this->assertSame('GET', $res);

        $res = $this->dispatch($controller, 'post');
        $this->assertSame($controller, $this->app->getController());
        $this->assertSame('post', $this->app->getAction());
        $this->assertSame('POST', $res);
    }

    protected function dispatch($controller, $action)
    {
        ob_start();
        $this->app->dispatch($controller, $action);
        return ob_get_clean();
    }

    protected function execute($action)
    {
        User::loginById(1);

        $app = wei()->app;
        $app->req->set('_format', 'json');

        $test = require __DIR__ . '/../Fixture/pages/tests/index.php';

        // 更改视图为测试的目录
        $origDirs = $app->view->getOption('dirs');
        $app->view->setDirs([dirname(__DIR__) . '/Fixture/views']);

        ob_start();
        try {
            // @phpstan-ignore-next-line 待整理出直接调用的方法
            $app->execute($test, $action);
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
