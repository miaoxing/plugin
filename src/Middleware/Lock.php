<?php

namespace Miaoxing\Plugin\Middleware;

use Miaoxing\Services\Middleware\BaseMiddleware;
use Wei\RetTrait;

/**
 * 对请求用户或IP加锁,控制页面只能由一个请求访问
 */
class Lock extends BaseMiddleware
{
    use RetTrait;

    /**
     * 加锁的名称,默认使用当前请求路径
     *
     * @var string
     */
    protected $name;

    /**
     * @var int
     */
    protected $expire = 30;

    /**
     * @var callable
     */
    protected $onFail;

    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        $name = $this->name ?: $this->request->getBaseUrl() . $this->request->getPathInfo();
        $key = 'lock-' . $this->getIdentifier() . '-' . $name;

        if (!wei()->lock($key, $this->expire)) {
            $this->onFail && call_user_func($this->onFail);
            return $this->err('您的操作过快，请稍候再试', -2001);
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
