<?php

namespace Miaoxing\Plugin\Middleware;

class HttpMethod extends Base
{
    /**
     * @var array
     */
    protected $actions;

    public function __invoke($next)
    {
        $action = $this->app->getAction();

        if (isset($this->actions[$action])) {
            $methods = array_map('strtoupper', (array) $this->actions[$action]);
            if (!in_array($this->request->getMethod(), $methods)) {
                return $this->response->json([
                    'code' => -405,
                    'message' => '请求方式不被允许',
                ]);
            }
        }

        return $next();
    }
}
