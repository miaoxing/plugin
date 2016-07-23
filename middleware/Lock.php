<?php

namespace miaoxing\plugin\middleware;

/**
 * 对请求用户或IP加锁,控制页面只能由一个请求访问
 */
class Lock extends Base
{
    /**
     * 加锁的名称,默认使用当前控制器行为
     *
     * @var string
     */
    protected $name;

    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        $name = $this->name ?: $this->app->getControllerAction();
        $key = 'lock-' . $this->getIdentifier() . '-' . $name;

        if (!wei()->lock($key)) {
            return $this->response->json([
                'code' => -2001,
                'message' => '您的操作过快，请稍候再试'
            ]);
        }

        return $next();
    }

    /**
     * 获取客户端唯一标识
     *
     * @return string
     */
    public function getIdentifier()
    {
        return wei()->curUser['id'] ?: $this->request->getServer('REMOTE_ADDR');
    }
}
