<?php

/**
 * @property    Miaoxing\Plugin\Service\App $app 应用
 * @method      mixed app($options = [])
 */
class AppMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\AppModel $appModel 应用模型
 * @method      Miaoxing\Plugin\Service\AppModel|Miaoxing\Plugin\Service\AppModel[] appModel($table = null)
 */
class AppModelMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\Event $event
 */
class EventMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\Model $model
 * @method      Miaoxing\Plugin\Service\Model|Miaoxing\Plugin\Service\Model[] model($table = null)
 */
class ModelMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
 */
class PluginMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\QueryBuilder $queryBuilder A SQL query builder class
 * @method      Miaoxing\Plugin\Service\QueryBuilder queryBuilder($table = null)
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
 * @property    Miaoxing\Plugin\Service\Str $str 字符串操作服务
 */
class StrMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\User $user 用户
 * @method      Miaoxing\Plugin\Service\User|Miaoxing\Plugin\Service\User[] user($table = null)
 */
class UserMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\UserModel $userModel
 * @method      Miaoxing\Plugin\Service\UserModel|Miaoxing\Plugin\Service\UserModel[] userModel($table = null)
 */
class UserModelMixin {
}

/**
 * @mixin AppMixin
 * @mixin AppModelMixin
 * @mixin EventMixin
 * @mixin ModelMixin
 * @mixin PluginMixin
 * @mixin QueryBuilderMixin
 * @mixin SessionMixin
 * @mixin StrMixin
 * @mixin UserMixin
 * @mixin UserModelMixin
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

/** @var Miaoxing\Plugin\Service\AppModel $appModel */
$app = wei()->appModel();

/** @var Miaoxing\Plugin\Service\AppModel|Miaoxing\Plugin\Service\AppModel[] $appModels */
$apps = wei()->appModel();

/** @var Miaoxing\Plugin\Service\Event $event */
$event = wei()->event;

/** @var Miaoxing\Plugin\Service\Model $model */
$model = wei()->model;

/** @var Miaoxing\Plugin\Service\Plugin $plugin */
$plugin = wei()->plugin;

/** @var Miaoxing\Plugin\Service\QueryBuilder $queryBuilder */
$queryBuilder = wei()->queryBuilder;

/** @var Miaoxing\Plugin\Service\Session $session */
$session = wei()->session;

/** @var Miaoxing\Plugin\Service\Str $str */
$str = wei()->str;

/** @var Miaoxing\Plugin\Service\User $user */
$user = wei()->user;

/** @var Miaoxing\Plugin\Service\UserModel $userModel */
$user = wei()->userModel();

/** @var Miaoxing\Plugin\Service\UserModel|Miaoxing\Plugin\Service\UserModel[] $userModels */
$users = wei()->userModel();
