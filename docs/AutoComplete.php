<?php

namespace miaoxing\plugin\docs {

    /**
     * @property    \miaoxing\plugin\services\Tester $tester
     * @method      \miaoxing\plugin\services\Tester tester()
     */
    class AutoComplete
    {
    }
}

namespace {
    /** @var \miaoxing\plugin\services\Plugin $plugin */
    $plugin = wei()->plugin;

    /** @var \Wei\Event $event */
    $event = wei()->event;

    /**
     * @return \miaoxing\plugin\docs\AutoComplete
     */
    function wei()
    {
    }
}
