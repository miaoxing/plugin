<?php

namespace MiaoxingDoc\Plugin {

    /**
     * @property    \Miaoxing\Plugin\Service\App $app 应用
     * @method      mixed app($options = [])
     *
     * @property    \Miaoxing\Plugin\Service\AppRecord $appRecord 应用模型
     * @method      \Miaoxing\Plugin\Service\AppRecord|\Miaoxing\Plugin\Service\AppRecord[] appRecord()
     *
     * @property    \Miaoxing\Services\Service\Asset $asset
     * @method      mixed asset($file, $version = true)
     *
     * @property    \Miaoxing\Services\Service\Cli $cli CLI
     *
     * @property    \Miaoxing\Services\Service\CliApp $cliApp CLI应用
     *
     * @property    \Miaoxing\Services\Service\Convention $convention
     *
     * @property    \Miaoxing\Plugin\Service\CurUser $curUser 当前用户
     * @method      \Miaoxing\Plugin\Service\CurUser|\Miaoxing\Plugin\Service\CurUser[] curUser()
     *
     * @property    \Miaoxing\Services\Service\Db $db
     * @method      Record db($table = null) Create a new instance of a SQL query builder with specified table name
     *
     * @property    \Miaoxing\Plugin\Service\Group $group 用户分组
     * @method      \Miaoxing\Plugin\Service\Group|\Miaoxing\Plugin\Service\Group[] group()
     *
     * @property    \Miaoxing\Services\Service\Http $http
     * @method      \Miaoxing\Services\Service\Http http($url = null, $options = []) Create a new HTTP object and execute
     *
     * @property    \Miaoxing\Services\Service\IsRecordExists $isRecordExists
     * @method      bool isRecordExists($input = null, $table = null, $field = 'id') Check if the input is existing table record
     *
     * @property    \Miaoxing\Plugin\Service\Laravel $laravel
     *
     * @property    \Miaoxing\Plugin\Service\Migration $migration 数据库迁移
     *
     * @property    \Miaoxing\Services\Service\OptionTrait $optionTrait
     *
     * @property    \Miaoxing\Services\Service\Page $page
     *
     * @property    \Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
     *
     * @property    \Miaoxing\Plugin\Service\Queue $queue
     *
     * @property    \Miaoxing\Services\Service\Request $request
     * @method      string|null request($name, $default = '') Returns a *stringify* or user defined($default) parameter value
     *
     * @property    \Miaoxing\Services\Service\Ret $ret
     * @method      mixed ret($message, $code = 1, $type = 'success')
     *
     * @property    \Miaoxing\Services\Service\ServiceTrait $serviceTrait
     *
     * @property    \Miaoxing\Services\Service\Str $str 字符串操作服务
     *
     * @property    \Miaoxing\Plugin\Service\Tester $tester 测试
     * @method      \Miaoxing\Plugin\Service\Tester tester($controller = null, $action = null)
     *
     * @property    \Miaoxing\Services\Service\Time $time 时间日期
     * @method      string time()
     *
     * @property    \Miaoxing\Plugin\Service\Url $url
     * @method      string url($url = '', $argsOrParams = [], $params = []) Generate the URL by specified URL and parameters
     *
     * @property    \Miaoxing\Plugin\Service\User $user 用户
     * @method      \Miaoxing\Plugin\Service\User|\Miaoxing\Plugin\Service\User[] user()
     *
     * @property    \Miaoxing\Services\Service\V $v A chaining validator
     * @method      \Miaoxing\Services\Service\V v($options = []) Create a new validator
     *
     * @property    \Miaoxing\Services\Service\View $view
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

    /** @var \Miaoxing\Services\Service\Asset $asset */
    $asset = wei()->asset;

    /** @var \Miaoxing\Services\Service\Cli $cli */
    $cli = wei()->cli;

    /** @var \Miaoxing\Services\Service\CliApp $cliApp */
    $cliApp = wei()->cliApp;

    /** @var \Miaoxing\Services\Service\Convention $convention */
    $convention = wei()->convention;

    /** @var Miaoxing\Plugin\Service\CurUser $curUser */
    $curUser = wei()->curUser;

    /** @var \Miaoxing\Services\Service\Db $db */
    $db = wei()->db;

    /** @var Miaoxing\Plugin\Service\Group $group */
    $group = wei()->group;

    /** @var \Miaoxing\Services\Service\Http $http */
    $http = wei()->http;

    /** @var \Miaoxing\Services\Service\IsRecordExists $isRecordExists */
    $isRecordExists = wei()->isRecordExists;

    /** @var Miaoxing\Plugin\Service\Laravel $laravel */
    $laravel = wei()->laravel;

    /** @var Miaoxing\Plugin\Service\Migration $migration */
    $migration = wei()->migration;

    /** @var \Miaoxing\Services\Service\OptionTrait $optionTrait */
    $optionTrait = wei()->optionTrait;

    /** @var \Miaoxing\Services\Service\Page $page */
    $page = wei()->page;

    /** @var Miaoxing\Plugin\Service\Plugin $plugin */
    $plugin = wei()->plugin;

    /** @var Miaoxing\Plugin\Service\Queue $queue */
    $queue = wei()->queue;

    /** @var \Miaoxing\Services\Service\Request $request */
    $request = wei()->request;

    /** @var \Miaoxing\Services\Service\Ret $ret */
    $ret = wei()->ret;

    /** @var \Miaoxing\Services\Service\ServiceTrait $serviceTrait */
    $serviceTrait = wei()->serviceTrait;

    /** @var \Miaoxing\Services\Service\Str $str */
    $str = wei()->str;

    /** @var Miaoxing\Plugin\Service\Tester $tester */
    $tester = wei()->tester;

    /** @var \Miaoxing\Services\Service\Time $time */
    $time = wei()->time;

    /** @var Miaoxing\Plugin\Service\Url $url */
    $url = wei()->url;

    /** @var Miaoxing\Plugin\Service\User $user */
    $user = wei()->user;

    /** @var \Miaoxing\Services\Service\V $v */
    $v = wei()->v;

    /** @var \Miaoxing\Services\Service\View $view */
    $view = wei()->view;
}
