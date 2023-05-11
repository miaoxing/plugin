<?php

/**
 * @property    Miaoxing\Plugin\Service\App $app 应用
 * @method      mixed app($options = [])
 */
class AppMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\App $app 应用
 */
class AppPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\AppModel $appModel 应用模型
 */
class AppModelMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\AppModel $appModel 应用模型
 */
class AppModelPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Config $config
 */
class ConfigMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Config $config
 */
class ConfigPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\ConfigModel $configModel 配置模型
 */
class ConfigModelMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\ConfigModel $configModel 配置模型
 */
class ConfigModelPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Fs $fs
 */
class FsMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Fs $fs
 */
class FsPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\GlobalConfigModel $globalConfigModel
 */
class GlobalConfigModelMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\GlobalConfigModel $globalConfigModel
 */
class GlobalConfigModelPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\IsBigIntString $isBigIntString
 * @method      mixed isBigIntString($input, $min = null, $max = null)
 */
class IsBigIntStringMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\IsBigIntString $isBigIntString
 */
class IsBigIntStringPropMixin
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
 * @property    Miaoxing\Plugin\Service\IsModelExists $isModelExists
 */
class IsModelExistsPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\IsUBigIntString $isUBigIntString
 * @method      mixed isUBigIntString($input, $min = null, $max = null)
 */
class IsUBigIntStringMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\IsUBigIntString $isUBigIntString
 */
class IsUBigIntStringPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Jwt $jwt
 */
class JwtMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Jwt $jwt
 */
class JwtPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\LocalStorage $localStorage
 */
class LocalStorageMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\LocalStorage $localStorage
 */
class LocalStoragePropMixin
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
 * @property    Miaoxing\Plugin\Service\ObjectReq $objectReq
 */
class ObjectReqPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\PageRouter $pageRouter
 */
class PageRouterMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\PageRouter $pageRouter
 */
class PageRouterPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
 */
class PluginMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Plugin $plugin 插件管理器
 */
class PluginPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Ret $ret   schema="Ret",
 * @method      Miaoxing\Plugin\Service\Ret ret($message, $code = null, $type = null) Return operation result data
 */
class RetMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Ret $ret   schema="Ret",
 */
class RetPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Seeder $seeder Seeder
 */
class SeederMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Seeder $seeder Seeder
 */
class SeederPropMixin
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
 * @property    Miaoxing\Plugin\Service\Session $session
 */
class SessionPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Snowflake $snowflake
 */
class SnowflakeMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Snowflake $snowflake
 */
class SnowflakePropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Storage $storage
 */
class StorageMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Storage $storage
 */
class StoragePropMixin
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
 * @property    Miaoxing\Plugin\Service\Tester $tester 测试
 */
class TesterPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Upload $upload
 * @method      bool upload($field = null, $options = []) Upload a file
 */
class UploadMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\Upload $upload
 */
class UploadPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\User $user 用户
 */
class UserMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\User $user 用户
 */
class UserPropMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\UserModel $userModel
 */
class UserModelMixin
{
}

/**
 * @property    Miaoxing\Plugin\Service\UserModel $userModel
 */
class UserModelPropMixin
{
}

/**
 * @mixin AppMixin
 * @mixin AppModelMixin
 * @mixin ConfigMixin
 * @mixin ConfigModelMixin
 * @mixin FsMixin
 * @mixin GlobalConfigModelMixin
 * @mixin IsBigIntStringMixin
 * @mixin IsModelExistsMixin
 * @mixin IsUBigIntStringMixin
 * @mixin JwtMixin
 * @mixin LocalStorageMixin
 * @mixin ObjectReqMixin
 * @mixin PageRouterMixin
 * @mixin PluginMixin
 * @mixin RetMixin
 * @mixin SeederMixin
 * @mixin SessionMixin
 * @mixin SnowflakeMixin
 * @mixin StorageMixin
 * @mixin TesterMixin
 * @mixin UploadMixin
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
