<?php

namespace MiaoxingDoc\Plugin {

    /**
     * @property    \Miaoxing\Plugin\Service\App $app 应用
     * @method      mixed app($options = [])
     *
     * @property    \Miaoxing\Plugin\Service\AppRecord $appRecord 应用模型
     * @method      \Miaoxing\Plugin\Service\AppRecord|\Miaoxing\Plugin\Service\AppRecord[] appRecord()
     *
     * @property    \Miaoxing\Plugin\Service\CurUser $curUser 当前用户
     * @method      \Miaoxing\Plugin\Service\CurUser|\Miaoxing\Plugin\Service\CurUser[] curUser()
     *
     * @property    \Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
     *
     * @property    \Miaoxing\Plugin\Service\Session $session
     * @method      mixed session($key, $value = null) Get or set session
     *
     * @property    \Miaoxing\Plugin\Service\User $user 用户
     * @method      \Miaoxing\Plugin\Service\User|\Miaoxing\Plugin\Service\User[] user()
     */
    class AutoComplete
    {
    }
}

namespace {

    /**
     * @return MiaoxingDoc\Plugin\AutoComplete
     */
    function wei()
    {
    }

    /** @var Miaoxing\Plugin\Service\App $app */
    $app = wei()->app;

    /** @var Miaoxing\Plugin\Service\AppRecord $appRecord */
    $appRecord = wei()->appRecord;

    /** @var Miaoxing\Plugin\Service\CurUser $curUser */
    $curUser = wei()->curUser;

    /** @var Miaoxing\Plugin\Service\Plugin $plugin */
    $plugin = wei()->plugin;

    /** @var Miaoxing\Plugin\Service\Session $session */
    $session = wei()->session;

    /** @var Miaoxing\Plugin\Service\User $user */
    $user = wei()->user;
}
