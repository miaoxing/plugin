<?php

/**
 * @property    Miaoxing\Plugin\Service\App $app 应用
 * @method      mixed app($options = [])
 */
class AppMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\AppModel $appModel 应用模型
 * @method      Miaoxing\Plugin\Service\AppModel appModel() 返回当前对象
 */
class AppModelMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Cls $cls The class util service
 */
class ClsMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Config $config
 */
class ConfigMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\ConfigModel $configModel 配置模型
 * @method      Miaoxing\Plugin\Service\ConfigModel configModel() 返回当前对象
 */
class ConfigModelMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Fs $fs
 */
class FsMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\GlobalConfigModel $globalConfigModel
 * @method      Miaoxing\Plugin\Service\GlobalConfigModel globalConfigModel() 返回当前对象
 */
class GlobalConfigModelMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\IsModelExists $isModelExists
 * @method      bool isModelExists($input = null, $model = null, $column = 'id') Check if the input is existing model
 */
class IsModelExistsMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Jwt $jwt
 */
class JwtMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\LocalStorage $localStorage
 */
class LocalStorageMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\ObjectReq $objectReq
 * @method      string|null objectReq($name, $default = '') Returns a *stringify* or user defined($default) parameter value
 */
class ObjectReqMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\PageRouter $pageRouter
 */
class PageRouterMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
 */
class PluginMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\QueryBuilder $queryBuilder A SQL query builder class
 */
class QueryBuilderMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Ret $ret
 * @method      Miaoxing\Plugin\Service\Ret ret($message, $code = null, $type = null) Return operation result data
 */
class RetMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Seeder $seeder Seeder
 */
class SeederMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Session $session
 * @method      mixed session($key, $value = null) Get or set session
 */
class SessionMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Snowflake $snowflake
 */
class SnowflakeMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Storage $storage
 */
class StorageMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Str $str The string util service
 */
class StrMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Tester $tester 测试
 * @method      static tester($controller = null, $action = null)
 */
class TesterMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\User $user 用户
 * @method      Miaoxing\Plugin\Service\User user() 返回当前对象
 */
class UserMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\UserModel $userModel
 * @method      Miaoxing\Plugin\Service\UserModel userModel() 返回当前对象
 */
class UserModelMixin
{
}

/**
 * @mixin AppMixin
 * @mixin AppModelMixin
 * @mixin ClsMixin
 * @mixin ConfigMixin
 * @mixin ConfigModelMixin
 * @mixin FsMixin
 * @mixin GlobalConfigModelMixin
 * @mixin IsModelExistsMixin
 * @mixin JwtMixin
 * @mixin LocalStorageMixin
 * @mixin ObjectReqMixin
 * @mixin PageRouterMixin
 * @mixin PluginMixin
 * @mixin QueryBuilderMixin
 * @mixin RetMixin
 * @mixin SeederMixin
 * @mixin SessionMixin
 * @mixin SnowflakeMixin
 * @mixin StorageMixin
 * @mixin StrMixin
 * @mixin TesterMixin
 * @mixin UserMixin
 * @mixin UserModelMixin
 */
class AutoCompletion
{
}

/**
 * @return AutoCompletion
 */
function wei()
{
    return new AutoCompletion();
}

/** @var Miaoxing\Plugin\Service\App $app */
$app = wei()->app;

/** @var Miaoxing\Plugin\Service\AppModel $app */
$app = wei()->appModel;

/** @var Miaoxing\Plugin\Service\AppModel|Miaoxing\Plugin\Service\AppModel[] $apps */
$apps = wei()->appModel();

/** @var Miaoxing\Plugin\Service\Cls $cls */
$cls = wei()->cls;

/** @var Miaoxing\Plugin\Service\Config $config */
$config = wei()->config;

/** @var Miaoxing\Plugin\Service\ConfigModel $config */
$config = wei()->configModel;

/** @var Miaoxing\Plugin\Service\ConfigModel|Miaoxing\Plugin\Service\ConfigModel[] $configs */
$configs = wei()->configModel();

/** @var Miaoxing\Plugin\Service\Fs $fs */
$fs = wei()->fs;

/** @var Miaoxing\Plugin\Service\GlobalConfigModel $globalConfig */
$globalConfig = wei()->globalConfigModel;

/** @var Miaoxing\Plugin\Service\GlobalConfigModel|Miaoxing\Plugin\Service\GlobalConfigModel[] $globalConfigs */
$globalConfigs = wei()->globalConfigModel();

/** @var Miaoxing\Plugin\Service\IsModelExists $isModelExists */
$isModelExists = wei()->isModelExists;

/** @var Miaoxing\Plugin\Service\Jwt $jwt */
$jwt = wei()->jwt;

/** @var Miaoxing\Plugin\Service\LocalStorage $localStorage */
$localStorage = wei()->localStorage;

/** @var Miaoxing\Plugin\Service\ObjectReq $objectReq */
$objectReq = wei()->objectReq;

/** @var Miaoxing\Plugin\Service\PageRouter $pageRouter */
$pageRouter = wei()->pageRouter;

/** @var Miaoxing\Plugin\Service\Plugin $plugin */
$plugin = wei()->plugin;

/** @var Miaoxing\Plugin\Service\QueryBuilder $queryBuilder */
$queryBuilder = wei()->queryBuilder;

/** @var Miaoxing\Plugin\Service\Ret $ret */
$ret = wei()->ret;

/** @var Miaoxing\Plugin\Service\Seeder $seeder */
$seeder = wei()->seeder;

/** @var Miaoxing\Plugin\Service\Session $session */
$session = wei()->session;

/** @var Miaoxing\Plugin\Service\Snowflake $snowflake */
$snowflake = wei()->snowflake;

/** @var Miaoxing\Plugin\Service\Storage $storage */
$storage = wei()->storage;

/** @var Miaoxing\Plugin\Service\Str $str */
$str = wei()->str;

/** @var Miaoxing\Plugin\Service\Tester $tester */
$tester = wei()->tester;

/** @var Miaoxing\Plugin\Service\User $user */
$user = wei()->user;

/** @var Miaoxing\Plugin\Service\UserModel $user */
$user = wei()->userModel;

/** @var Miaoxing\Plugin\Service\UserModel|Miaoxing\Plugin\Service\UserModel[] $users */
$users = wei()->userModel();
