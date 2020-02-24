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
     * @property    \Miaoxing\Plugin\Service\Group $group 用户分组
     * @method      \Miaoxing\Plugin\Service\Group|\Miaoxing\Plugin\Service\Group[] group()
     *
     * @property    \Miaoxing\Plugin\Service\Laravel $laravel
     *
     * @property    \Miaoxing\Services\Service\Migration $migration 数据库迁移
     *
     * @property    \Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
     *
     * @property    \Miaoxing\Plugin\Service\Tester $tester 测试
     * @method      \Miaoxing\Plugin\Service\Tester tester($controller = null, $action = null)
     *
     * @property    \Miaoxing\Services\Service\Url $url
     * @method      string url($url = '', $argsOrParams = [], $params = []) Generate the URL by specified URL and parameters
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

    /** @var Miaoxing\Plugin\Service\Group $group */
    $group = wei()->group;

    /** @var Miaoxing\Plugin\Service\Laravel $laravel */
    $laravel = wei()->laravel;

    /** @var \Miaoxing\Services\Service\Migration $migration */
    $migration = wei()->migration;

    /** @var Miaoxing\Plugin\Service\Plugin $plugin */
    $plugin = wei()->plugin;

    /** @var Miaoxing\Plugin\Service\Tester $tester */
    $tester = wei()->tester;

    /** @var \Miaoxing\Services\Service\Url $url */
    $url = wei()->url;

    /** @var Miaoxing\Plugin\Service\User $user */
    $user = wei()->user;
}
