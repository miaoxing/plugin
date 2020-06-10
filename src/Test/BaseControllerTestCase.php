<?php

namespace Miaoxing\Plugin\Test;

use Miaoxing\Plugin\Service\Tester;
use Miaoxing\Plugin\Service\User;
use RuntimeException;

/**
 * @mixin \AppMixin
 */
abstract class BaseControllerTestCase extends BaseTestCase
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
     * @param int|null $code
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
        $controllerClasses = $this->app->getControllerClasses($controller);
        $controllerClass = end($controllerClasses);
        $actions = get_class_methods($controllerClass);
        if (null === $actions) {
            throw new RuntimeException(sprintf(
                'Action method not found in controller %s class %s',
                $controller,
                $controllerClass
            ));
        }

        $params = [];
        foreach ($actions as $action) {
            if ('Action' === substr($action, -6)) {
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
            preg_match('/Controller\\\\(.+?)ControllerTest/', static::class, $matches);
            $values = [];
            foreach (explode('\\', $matches[1]) as $value) {
                $values[] = lcfirst($value);
            }
            $this->controller = implode('\\', $values);
        }

        return $this->controller;
    }

    /**
     * @param string $controller
     * @param string $action
     * @return \Exception|\Wei\Response
     */
    public function dispatch($controller, $action = 'index')
    {
        User::loginById(1);

        ob_start();
        try {
            $response = $this->app->dispatch($controller, $action);
        } catch (\Exception $e) {
            ob_end_clean();

            return $e;
        }

        ob_end_clean();

        return $response;
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

        return wei()->tester($controller, $action)
            ->request($request);
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
