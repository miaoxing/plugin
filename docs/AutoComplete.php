<?php

namespace MiaoxingDoc\Plugin {

    /**
     * @property    \Miaoxing\Plugin\Service\App $app 应用
     * @method      mixed app($options = [])
     *
     * @property    \Miaoxing\Plugin\Service\AppRecord $appRecord 应用模型
     * @method      \Miaoxing\Plugin\Service\AppRecord|\Miaoxing\Plugin\Service\AppRecord[] appRecord()
     *
     * @property    \Miaoxing\Plugin\Service\Asset $asset
     * @method      mixed asset($file, $version = true)
     *
     * @property    \Miaoxing\Plugin\Service\Cli $cli CLI
     *
     * @property    \Miaoxing\Plugin\Service\CliApp $cliApp CLI应用
     *
     * @property    \Miaoxing\Plugin\Service\Convention $convention
     *
     * @property    \Miaoxing\Plugin\Service\CurUser $curUser 当前用户
     * @method      \Miaoxing\Plugin\Service\CurUser|\Miaoxing\Plugin\Service\CurUser[] curUser()
     *
     * @property    \Miaoxing\Plugin\Service\Db $db
     * @method      Record db($table = null) Create a new instance of a SQL query builder with specified table name
     *
     * @property    \Miaoxing\Plugin\Service\Group $group 用户分组
     * @method      \Miaoxing\Plugin\Service\Group|\Miaoxing\Plugin\Service\Group[] group()
     *
     * @property    \Miaoxing\Plugin\Service\Http $http
     * @method      \Miaoxing\Plugin\Service\Http http($url = null, $options = []) Create a new HTTP object and execute
     *
     * @property    \Miaoxing\Plugin\Service\IsRecordExists $isRecordExists
     * @method      bool isRecordExists($input = null, $table = null, $field = 'id') Check if the input is existing table record
     *
     * @property    \Miaoxing\Plugin\Service\Migration $migration 数据库迁移
     *
     * @property    \Miaoxing\Plugin\Service\OptionTrait $optionTrait
     *
     * @property    \Miaoxing\Plugin\Service\Page $page
     *
     * @property    \Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
     *
     * @property    \Miaoxing\Plugin\Service\Request $request
     * @method      string|null request($name, $default = '') Returns a *stringify* or user defined($default) parameter value
     *
     * @property    \Miaoxing\Plugin\Service\Ret $ret
     * @method      mixed ret($message, $code = 1, $type = 'success')
     *
     * @property    \Miaoxing\Plugin\Service\ServiceTrait $serviceTrait
     *
     * @property    \Miaoxing\Plugin\Service\Setting $setting 设置
     * @method      string|\Miaoxing\Plugin\Service\Setting|\Miaoxing\Plugin\Service\Setting[] setting($id = null, $default = null)
     *
     * @property    \Miaoxing\Plugin\Service\Str $str 字符串操作服务
     *
     * @property    \Miaoxing\Plugin\Service\Tester $tester 测试
     * @method      \Miaoxing\Plugin\Service\Tester tester($controller = null, $action = null)
     *
     * @property    \Miaoxing\Plugin\Service\Time $time 时间日期
     * @method      string time()
     *
     * @property    \Miaoxing\Plugin\Service\UrlMapper $urlMapper URL映射
     * @method      mixed urlMapper()
     *
     * @property    \Miaoxing\Plugin\Service\User $user 用户
     * @method      \Miaoxing\Plugin\Service\User|\Miaoxing\Plugin\Service\User[] user()
     *
     * @property    \Miaoxing\Plugin\Service\V $v A chaining validator
     * @method      \Miaoxing\Plugin\Service\V v($options = []) Create a new validator
     *
     * @property    \Miaoxing\Plugin\Service\View $view
     * @method      string view($name = null, $data = []) Render a PHP template
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

    /** @var Miaoxing\Plugin\Service\Asset $asset */
    $asset = wei()->asset;

    /** @var Miaoxing\Plugin\Service\Cli $cli */
    $cli = wei()->cli;

    /** @var Miaoxing\Plugin\Service\CliApp $cliApp */
    $cliApp = wei()->cliApp;

    /** @var Miaoxing\Plugin\Service\Convention $convention */
    $convention = wei()->convention;

    /** @var Miaoxing\Plugin\Service\CurUser $curUser */
    $curUser = wei()->curUser;

    /** @var Miaoxing\Plugin\Service\Db $db */
    $db = wei()->db;

    /** @var Miaoxing\Plugin\Service\Group $group */
    $group = wei()->group;

    /** @var Miaoxing\Plugin\Service\Http $http */
    $http = wei()->http;

    /** @var Miaoxing\Plugin\Service\IsRecordExists $isRecordExists */
    $isRecordExists = wei()->isRecordExists;

    /** @var Miaoxing\Plugin\Service\Migration $migration */
    $migration = wei()->migration;

    /** @var Miaoxing\Plugin\Service\OptionTrait $optionTrait */
    $optionTrait = wei()->optionTrait;

    /** @var Miaoxing\Plugin\Service\Page $page */
    $page = wei()->page;

    /** @var Miaoxing\Plugin\Service\Plugin $plugin */
    $plugin = wei()->plugin;

    /** @var Miaoxing\Plugin\Service\Request $request */
    $request = wei()->request;

    /** @var Miaoxing\Plugin\Service\Ret $ret */
    $ret = wei()->ret;

    /** @var Miaoxing\Plugin\Service\ServiceTrait $serviceTrait */
    $serviceTrait = wei()->serviceTrait;

    /** @var Miaoxing\Plugin\Service\Setting $setting */
    $setting = wei()->setting;

    /** @var Miaoxing\Plugin\Service\Str $str */
    $str = wei()->str;

    /** @var Miaoxing\Plugin\Service\Tester $tester */
    $tester = wei()->tester;

    /** @var Miaoxing\Plugin\Service\Time $time */
    $time = wei()->time;

    /** @var Miaoxing\Plugin\Service\UrlMapper $urlMapper */
    $urlMapper = wei()->urlMapper;

    /** @var Miaoxing\Plugin\Service\User $user */
    $user = wei()->user;

    /** @var Miaoxing\Plugin\Service\V $v */
    $v = wei()->v;

    /** @var Miaoxing\Plugin\Service\View $view */
    $view = wei()->view;
}
