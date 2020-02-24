<?php

namespace Miaoxing\Plugin {
    /**
     * @property    \Wei\Event $event
     * @property    \Miaoxing\Plugin\Service\App $app 应用管理服务
     * @property    \Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
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
