<?php

namespace Miaoxing\Plugin\Middleware;

/**
 * @property \Miaoxing\Plugin\Service\App $app
 * @property \Wei\Request $request
 * @property \Wei\Response $response
 */
abstract class Base extends \Wei\Base
{
    /**
     * Execute the middleware
     *
     * @param callable $next
     */
    abstract public function __invoke($next);

    /**
     * Returns current class name
     *
     * 兼容5.5之前版本获取类名的方法
     *
     * @return string
     */
    public static function className()
    {
        return get_called_class();
    }
}
