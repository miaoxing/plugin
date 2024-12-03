<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Auth\BaseAuth;
use Miaoxing\Plugin\Auth\JwtAuth;
use Miaoxing\Plugin\BaseService;
use Miaoxing\Plugin\ConfigTrait;
use Wei\Ret;
use Wei\Time;

/**
 * 用户
 *
 * @property bool $enableRegister
 * @property string $disableRegisterTips
 * @property bool $enableLoginCaptcha
 * @property int $defaultGroupId
 * @property bool $enablePasswordRest
 * @property bool $enableMobileVerify
 * @property bool $enableLogin
 * @property string $disableLoginTips
 * @property string $bgImage
 * @property int $defaultTagId
 * @property int $agreementArticleId
 * @property bool $enableExport
 * @property bool $enableCreate
 * @property string $defaultAvatar
 * @property bool $enablePinCode
 * @mixin \EventMixin
 * @mixin \ReqMixin
 */
class User extends BaseService
{
    use ConfigTrait {
        __get as getConfig;
    }

    /**
     * 用户服务是唯一的
     *
     * @var bool
     */
    protected static $createNewInstance = false;

    /**
     * @var UserModel|null
     */
    protected $cur;

    /**
     * @experimental expected to change
     */
    protected $configs = [
        'enablePinCode' => [
            'default' => false,
        ],
        'checkMobileUnique' => [
            'default' => false,
        ],
        'defaultAvatar' => [
            'default' => '',
        ],
        'enableLogin' => [
            'default' => true,
        ],
        'enableRegister' => [
            'default' => true,
        ],
        'disableRegisterTips' => [
            'default' => '注册功能未启用',
        ],
        'enableLoginCaptcha' => [
            'default' => false,
        ],
        'defaultGroupId' => [
            'default' => 0,
        ],
        'enablePasswordRest' => [
            'default' => false,
        ],
        'enableMobileVerify' => [
            'default' => false,
        ],
        'disableLoginTips' => [
            'default' => '登录功能未启用',
        ],
        'bgImage' => [
            'default' => '',
        ],
        'defaultTagId' => [],
        'agreementArticleId' => [],
        'enableExport' => [
            'default' => false,
        ],
        'enableCreate' => [
            'default' => false,
        ],
    ];

    /**
     * @var string
     */
    protected $authClass = JwtAuth::class;

    /**
     * @var BaseAuth|null
     */
    protected $auth;

    /**
     * @var array
     * @internal
     */
    protected $columns = [];

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!$this->columns) {
            $this->columns = UserModel::getColumns();
        }
        if (isset($this->columns[$name])) {
            return $this->get($name);
        }

        return $this->getConfig($name);
    }

    /**
     * @param array|callable $returnFields
     * @param callable|null $prepend
     * @return array
     * @experimental may be remove
     * @svc
     */
    protected function toArray($returnFields = [], ?callable $prepend = null): array
    {
        return $this->cur()->toArray($returnFields, $prepend);
    }

    /**
     * @param iterable $attributes
     * @return UserModel
     * @experimental may be remove
     * @svc
     */
    protected function save(iterable $attributes = []): UserModel
    {
        return $this->cur()->save($attributes);
    }

    /**
     * 获取用户资料，优先从认证服务中获取
     *
     * @param string $name
     * @return mixed
     * @svc
     */
    protected function get(string $name)
    {
        $data = $this->getAuth()->getData();
        if (isset($data[$name])) {
            return $data[$name];
        }

        $user = $this->cur();
        if ($user) {
            return $user->get($name);
        }

        return null;
    }

    /**
     * Return the current user id
     *
     * @return int|string|null
     * @svc
     */
    protected function id()
    {
        return $this->get('id');
    }

    /**
     * Return the current user model
     *
     * @return UserModel
     * @svc
     */
    protected function cur(): ?UserModel
    {
        if (!$this->cur) {
            $this->loadDbUser();
        }
        return $this->cur;
    }

    /**
     * 判断用户是否登录
     *
     * @return bool
     * @svc
     */
    protected function isLogin(): bool
    {
        return $this->getAuth()->isLogin();
    }

    /**
     * 检查用户是否登录
     *
     * @return Ret
     * @svc
     */
    protected function checkLogin(): Ret
    {
        return $this->getAuth()->checkLogin();
    }

    /**
     * 根据用户账号密码,登录用户
     *
     * @param mixed $data
     * @return Ret
     * @svc
     */
    protected function login($data): Ret
    {
        // 1. 校验用户账号密码是否符合规则
        $validator = wei()->validate([
            'data' => $data,
            'rules' => [
                'username' => [
                    'required' => true,
                ],
                'password' => [
                    'required' => true,
                ],
            ],
            'names' => [
                'username' => '帐号',
                'password' => '密码',
            ],
        ]);

        if (!$validator->isValid()) {
            return err($validator->getFirstMessage());
        }

        // 2. 检查手机/邮箱/用户名是否存在
        $user = UserModel::new();
        switch (true) {
            case wei()->isMobileCn($data['username']):
                $column = 'mobile';
                $user->whereNotNULL('mobile_verified_at');
                break;

            case wei()->isEmail($data['username']):
                $column = 'email';
                break;

            default:
                $column = 'username';
        }

        $user = $user->findBy($column, $data['username']);

        if (!$user) {
            return err('用户名不存在或密码错误');
        }

        // 3. 检查用户是否有效
        if (!$user->isEnabled) {
            return err('用户未启用,无法登录');
        }

        // 4. 验证密码是否正确
        if (!$user->verifyPassword($data['password'])) {
            return err('用户不存在或密码错误');
        }

        // 5. 验证通过,登录用户
        $ret = $this->loginByModel($user);
        if ($ret->isErr()) {
            return $ret;
        }

        $user->lastLoginAt = Time::now();
        $user->save();
        return $ret;
    }

    /**
     * 根据用户ID直接登录用户
     *
     * @param string|int $id
     * @return Ret
     * @svc
     */
    protected function loginById($id): Ret
    {
        $user = UserModel::find($id);
        if (!$user) {
            return err('用户不存在');
        } else {
            return $this->loginByModel($user);
        }
    }

    /**
     * 根据条件查找或创建用户,并登录
     *
     * @param array $conditions
     * @param array|object $data
     * @return $this
     * @svc
     */
    protected function loginBy(array $conditions, $data = []): self
    {
        $user = UserModel::findOrInitBy($conditions, $data);
        $this->loginByModel($user);

        return $this;
    }

    /**
     * 根据用户对象登录用户
     *
     * @param UserModel $user
     * @return Ret
     * @svc
     */
    protected function loginByModel(UserModel $user): Ret
    {
        $ret = $this->getAuth()->login($user);
        if ($ret->isSuc()) {
            $this->setCur($user);
            $this->event->trigger('login', [$user]);
            // @deprecated
            $this->event->trigger('userLogin', [$user]);
        }

        return $ret;
    }

    /**
     * 销毁用户会话,退出登录
     *
     * @return Ret
     * @svc
     */
    protected function logout(): Ret
    {
        if (!$this->isLogin()) {
            return err('用户未登录');
        }

        $this->getAuth()->logout();
        $this->event->trigger('logout', [$this->cur()]);
        // @@deprecated
        $this->event->trigger('beforeUserLogout', [$this->cur()]);
        $this->setCur(null);

        return suc();
    }

    /**
     * 当用户信息更改后,可以主动调用该方法,刷新会话中的数据
     *
     * @param UserModel $user
     * @return $this
     * @svc
     */
    protected function refresh(UserModel $user): self
    {
        if ($user->id === ($this->getAuth()->getData()['id'] ?? null)) {
            $this->loginByModel($user);
        }

        return $this;
    }

    /**
     * @return BaseAuth
     */
    protected function getAuth(): BaseAuth
    {
        if (!$this->auth) {
            $this->auth = new $this->authClass();
        }
        return $this->auth;
    }

    /**
     * Set the current user model
     *
     * @param UserModel|null $user
     * @return $this
     */
    protected function setCur(?UserModel $user): self
    {
        $this->cur = $user;
        return $this;
    }

    /**
     * 从数据库中查找用户加载到当前记录中
     *
     * @internal
     */
    protected function loadDbUser()
    {
        if (!$this->isLogin()) {
            return;
        }

        $id = $this->getAuth()->getData()['id'] ?? null;
        $user = UserModel::new();
        $user->setCacheKey($user->getModelCacheKey($id))->findOrInit($id);
        $this->setCur($user);
    }
}
