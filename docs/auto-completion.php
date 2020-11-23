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
 * @property    Miaoxing\Plugin\Service\Config $config
 */
class ConfigMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\IsModelExists $isModelExists
 * @method      bool isModelExists($input = null, $model = null, $column = 'id') Check if the input is existing model
 */
class IsModelExistsMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\Jwt $jwt
 */
class JwtMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\Model $model
 * @method      Miaoxing\Plugin\Service\Model|Miaoxing\Plugin\Service\Model[] model($table = null)
 */
class ModelMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\PageRouter $pageRouter
 */
class PageRouterMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
 */
class PluginMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\QueryBuilder $queryBuilder A SQL query builder class
 * @method      static queryBuilder($table = null)
 */
class QueryBuilderMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\Ret $ret
 * @method      Miaoxing\Plugin\Service\Ret ret($message, $code = null, $type = null) Return operation result data
 */
class RetMixin {
}

/**
 * @property    Miaoxing\Plugin\Service\Schema $schema
 */
class SchemaMixin {
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
 * @property    Miaoxing\Plugin\Service\Tester $tester 测试
 * @method      static tester($controller = null, $action = null)
 */
class TesterMixin {
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
 * @mixin ConfigMixin
 * @mixin IsModelExistsMixin
 * @mixin JwtMixin
 * @mixin ModelMixin
 * @mixin PageRouterMixin
 * @mixin PluginMixin
 * @mixin QueryBuilderMixin
 * @mixin RetMixin
 * @mixin SchemaMixin
 * @mixin SessionMixin
 * @mixin StrMixin
 * @mixin TesterMixin
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

/** @var Miaoxing\Plugin\Service\Config $config */
$config = wei()->config;

/** @var Miaoxing\Plugin\Service\IsModelExists $isModelExists */
$isModelExists = wei()->isModelExists;

/** @var Miaoxing\Plugin\Service\Jwt $jwt */
$jwt = wei()->jwt;

/** @var Miaoxing\Plugin\Service\Model $model */
$model = wei()->model;

/** @var Miaoxing\Plugin\Service\PageRouter $pageRouter */
$pageRouter = wei()->pageRouter;

/** @var Miaoxing\Plugin\Service\Plugin $plugin */
$plugin = wei()->plugin;

/** @var Miaoxing\Plugin\Service\QueryBuilder $queryBuilder */
$queryBuilder = wei()->queryBuilder;

/** @var Miaoxing\Plugin\Service\Ret $ret */
$ret = wei()->ret;

/** @var Miaoxing\Plugin\Service\Schema $schema */
$schema = wei()->schema;

/** @var Miaoxing\Plugin\Service\Session $session */
$session = wei()->session;

/** @var Miaoxing\Plugin\Service\Str $str */
$str = wei()->str;

/** @var Miaoxing\Plugin\Service\Tester $tester */
$tester = wei()->tester;

/** @var Miaoxing\Plugin\Service\User $user */
$user = wei()->user;

/** @var Miaoxing\Plugin\Service\UserModel $userModel */
$user = wei()->userModel();

/** @var Miaoxing\Plugin\Service\UserModel|Miaoxing\Plugin\Service\UserModel[] $userModels */
$users = wei()->userModel();
