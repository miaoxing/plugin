<?php

namespace Miaoxing\Plugin;

use Wei\BaseController;

/**
 * @mixin \EventMixin
 */
abstract class BasePage extends BaseController
{
    /**
     * Initialize the page, can be used to register middleware
     */
    public function init()
    {
        $this->event->trigger('pageInit', [$this]);
    }
}
