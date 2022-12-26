<?php

namespace Miaoxing\Plugin;

use Wei\RetTrait;

/**
 * @mixin \UserMixin
 * @mixin \AppMixin
 * @mixin \SessionMixin
 * @mixin \ViewMixin
 * @mixin \LoggerMixin
 * @mixin \PluginMixin
 * @mixin \RetMixin
 * @mixin \EventMixin
 * @property bool $controllerAuth
 * @property array $actionAuths
 */
abstract class BaseController extends \Wei\BaseController
{
    use HandleRetTrait;
    use RetTrait;

    /**
     * Initialize the controller, can be used to register middleware
     */
    public function init()
    {
        $this->event->trigger('controllerInit', [$this]);
    }
}
