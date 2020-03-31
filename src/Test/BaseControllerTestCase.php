<?php

namespace Miaoxing\Plugin\Test;

use Miaoxing\Plugin\Service\User;
use Miaoxing\Services\Service\Tester;

class BaseControllerTestCase extends BaseTestCase
{
    /**
     * 当前控制器名称
     *
     * @var string
     */
    protected $controller;

    /**
     * 预期action返回的code
     *
     * @var array
     */
    protected $statusCodes = [];

    /**
     * 页面可以正常访问
     *
     * @dataProvider providerForActions
     * @param string $action
     * @param null|int $code
     */
    public function testActions($action, $code = null)
    {
        $controller = $this->getController();

        $result = $this->dispatch($controller, $action);

        if ($result instanceof \Exception) {
            $this->assertEquals(404, $result->getCode(), $action . '返回' . $result->getMessage());
        } else {
            if ($code) {
                $this->assertEquals($code, $result->getStatusCode());
            } else {
                $this->assertNotEquals(302, $result->getStatusCode());
            }
            $this->assertNotEmpty($result->getContent(), $action . '返回内容不为空');
        }
    }

    public function providerForActions()
    {
        $controller = $this->getController();
        $controllerClasses = wei()->app->getControllerClasses($controller);
        $controllerClass = array_pop($controllerClasses);
        $actions = get_class_methods($controllerClass);

        if ($actions === null) {
            throw new \Exception(sprintf(
                'Action method not found in controller %s class %s',
                $controller,
                $controllerClass
            ));
        }

        $params = [];
        foreach ($actions as $action) {
            if (substr($action, -6) == 'Action') {
                $action = substr($action, 0, -6);
                $params[] = [
                    $action,
                    isset($this->statusCodes[$action]) ? $this->statusCodes[$action] : null,
                ];
            }
        }

        return $params;
    }

    public function getController()
    {
        if (!$this->controller) {
            $class = get_called_class();
            $parts = explode('Controller\\', $class);
            $controller = substr($parts[1], 0, -4);
            $this->controller = implode('/', array_map('lcfirst', explode('\\', $controller)));
        }

        return $this->controller;
    }

    protected function getAction()
    {
        preg_match('/test(.+?)Action/', $this->getName(), $matches);
        if (!isset($matches[1])) {
            throw new \Exception('Invalid test case naming');
        }

        return lcfirst($matches[1]);
    }

    /**
     * @param array $request
     * @return Tester
     */
    protected function visitCurPage(array $request = [])
    {
        $controller = $this->getController();
        $action = $this->getAction();
        $this->step('访问页面' . $controller . '/' . $action);

        return wei()->tester($controller, $action)
            ->request($request);
    }

    /**
     * @param string $controller
     * @param string $action
     * @return \Wei\Response|\Exception
     */
    public function dispatch($controller, $action = 'index')
    {
        $wei = wei();

        User::loginById(1);

        $wei->setNamespace('test');

        ob_start();
        try {
            $response = $wei->app->dispatch($controller, $action);
        } catch (\Exception $e) {
            ob_end_clean();

            return $e;
        }

        ob_end_clean();

        return $response;
    }

    /**
     * 运行任务
     *
     * @param string $name
     * @param array $data
     * @return array
     */
    protected function runJob($name, array $data = [])
    {
        $parts = explode('/', $name);
        $action = array_pop($parts);
        $controller = implode('/', $parts);

        return wei()->tester()
            ->login(1)
            ->controller($controller)
            ->action($action)
            ->request($data)
            ->json()
            ->exec()
            ->response();
    }
}
