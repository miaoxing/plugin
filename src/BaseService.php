<?php

namespace Miaoxing\Plugin {
    /**
     * @mixin \AppMixin
     * @mixin \PluginMixin
     * @mixin \EventMixin
     */
    class BaseService extends \Wei\Base
    {
    }
}

namespace {
    if (!function_exists('wei')) {
        /**
         * @return \Miaoxing\Plugin\BaseService
         */
        function wei()
        {
        }
    }
}
