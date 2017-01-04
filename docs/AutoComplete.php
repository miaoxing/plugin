<?php

namespace miaoxing\plugin\docs {

    /**
     * @property    \Miaoxing\Plugin\Service\Tester $tester
     * @method      \Miaoxing\Plugin\Service\Tester tester()
     *
     * @property    \Miaoxing\Plugin\Service\Schema $schema
     */
    class AutoComplete
    {
    }
}

namespace {

    /** @var \Miaoxing\Plugin\Service\Plugin $plugin */
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
