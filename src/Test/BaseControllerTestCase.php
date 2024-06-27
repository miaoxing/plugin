<?php

namespace Miaoxing\Plugin\Test;

use Miaoxing\Plugin\Service\Tester;
use Miaoxing\Plugin\Service\User;

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

    public function getController()
    {
        if (!$this->controller) {
            preg_match('/Controller\\\(.+?)ControllerTest/', static::class, $matches);
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
     * @return \Exception|\Wei\Res
     */
    public function dispatch($controller, $action = 'index')
    {
        User::loginById(1);

        ob_start();
        try {
            $res = $this->app->dispatch($controller, $action);
        } catch (\Exception $e) {
            ob_end_clean();

            return $e;
        }

        ob_end_clean();

        return $res;
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
            ->req($data)
            ->json()
            ->exec()
            ->response();
    }
}
