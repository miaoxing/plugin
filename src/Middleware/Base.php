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
}
