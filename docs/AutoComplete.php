<?php

namespace MiaoxingDoc\Plugin {

    /**
     * @property    \Miaoxing\Plugin\Service\App $app 应用
     * @method      mixed app($options = []) 
     * @see         \Miaoxing\Plugin\Service\App::__invoke
     *
     * @property    \Miaoxing\Plugin\Service\AppRecord $appRecord 应用模型
     * @method      \Miaoxing\Plugin\Service\AppRecord|\Miaoxing\Plugin\Service\AppRecord[] appRecord() 
     * @see         \Miaoxing\Plugin\Service\AppRecord::__invoke
     *
     * @property    \Miaoxing\Plugin\Service\Cli $cli CLI
     *
     * @property    \Miaoxing\Plugin\Service\CliApp $cliApp CLI应用
     *
     * @property    \Miaoxing\Plugin\Service\CurUser $curUser 当前用户
     * @method      \Miaoxing\Plugin\Service\CurUser|\Miaoxing\Plugin\Service\CurUser[] curUser() 
     * @see         \Miaoxing\Plugin\Service\CurUser::__invoke
     *
     * @property    \Miaoxing\Plugin\Service\Group $group 用户分组
     * @method      \Miaoxing\Plugin\Service\Group|\Miaoxing\Plugin\Service\Group[] group() 
     * @see         \Miaoxing\Plugin\Service\Group::__invoke
     *
     * @property    \Miaoxing\Plugin\Service\Migration $migration 数据库迁移
     *
     * @property    \Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
     *
     * @property    \Miaoxing\Plugin\Service\Setting $setting 设置
     * @method      string|\Miaoxing\Plugin\Service\Setting|\Miaoxing\Plugin\Service\Setting[] setting($id = null, $default = null) 
     * @see         \Miaoxing\Plugin\Service\Setting::__invoke
     *
     * @property    \Miaoxing\Plugin\Service\Tester $tester 测试
     * @method      \Miaoxing\Plugin\Service\Tester tester($controller = null, $action = null) 
     * @see         \Miaoxing\Plugin\Service\Tester::__invoke
     *
     * @property    \Miaoxing\Plugin\Service\UrlMapper $urlMapper URL映射
     * @method      mixed urlMapper() 
     * @see         \Miaoxing\Plugin\Service\UrlMapper::__invoke
     *
     * @property    \Miaoxing\Plugin\Service\User $user 用户
     * @method      \Miaoxing\Plugin\Service\User|\Miaoxing\Plugin\Service\User[] user() 
     * @see         \Miaoxing\Plugin\Service\User::__invoke
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

    /** @var Miaoxing\Plugin\Service\Cli $cli */
    $cli = wei()->cli;

    /** @var Miaoxing\Plugin\Service\CliApp $cliApp */
    $cliApp = wei()->cliApp;

    /** @var Miaoxing\Plugin\Service\CurUser $curUser */
    $curUser = wei()->curUser;

    /** @var Miaoxing\Plugin\Service\Group $group */
    $group = wei()->group;

    /** @var Miaoxing\Plugin\Service\Migration $migration */
    $migration = wei()->migration;

    /** @var Miaoxing\Plugin\Service\Plugin $plugin */
    $plugin = wei()->plugin;

    /** @var Miaoxing\Plugin\Service\Setting $setting */
    $setting = wei()->setting;

    /** @var Miaoxing\Plugin\Service\Tester $tester */
    $tester = wei()->tester;

    /** @var Miaoxing\Plugin\Service\UrlMapper $urlMapper */
    $urlMapper = wei()->urlMapper;

    /** @var Miaoxing\Plugin\Service\User $user */
    $user = wei()->user;
}
