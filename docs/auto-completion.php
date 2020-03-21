<?php

/**
 * @property    Miaoxing\Plugin\Service\App $app 应用
 * @method      mixed app($options = [])
 */
class AppMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\AppRecord $appRecord 应用模型
 * @method      \Miaoxing\Plugin\Service\AppRecord|\Miaoxing\Plugin\Service\AppRecord[] appRecord()
 */
class AppRecordMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\CurUser $curUser 当前用户
 * @method      \Miaoxing\Plugin\Service\CurUser|\Miaoxing\Plugin\Service\CurUser[] curUser()
 */
class CurUserMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
 */
class PluginMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\QueryBuilder $queryBuilder A SQL query builder class
 * @method      \Miaoxing\Plugin\Service\QueryBuilder queryBuilder($table = null)
 */
class QueryBuilderMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\Session $session
 * @method      mixed session($key, $value = null) Get or set session
 */
class SessionMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\User $user 用户
 * @method      \Miaoxing\Plugin\Service\User|\Miaoxing\Plugin\Service\User[] user()
 */
class UserMixin {
}

/**
 * @mixin AppMixin
 * @mixin AppRecordMixin
 * @mixin CurUserMixin
 * @mixin PluginMixin
 * @mixin QueryBuilderMixin
 * @mixin SessionMixin
 * @mixin UserMixin
 */
class AutoCompletion {
}

/**
 * @return AutoCompletion
 */
function wei()
{
    return new AutoCompletion;
}

/** @var Miaoxing\Plugin\Service\App $app */
$app = wei()->app;

/** @var Miaoxing\Plugin\Service\AppRecord $appRecord */
$appRecord = wei()->appRecord;

/** @var Miaoxing\Plugin\Service\CurUser $curUser */
$curUser = wei()->curUser;

/** @var Miaoxing\Plugin\Service\Plugin $plugin */
$plugin = wei()->plugin;

/** @var Miaoxing\Plugin\Service\QueryBuilder $queryBuilder */
$queryBuilder = wei()->queryBuilder;

/** @var Miaoxing\Plugin\Service\Session $session */
$session = wei()->session;

/** @var Miaoxing\Plugin\Service\User $user */
$user = wei()->user;
