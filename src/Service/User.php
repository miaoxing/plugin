<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Auth\BaseAuth;
use Miaoxing\Plugin\Auth\JwtAuth;
use Miaoxing\Plugin\ConfigTrait;

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
 * @mixin \PasswordMixin
 */
class User extends UserModel
{
    use ConfigTrait {
        __get as getConfig;
    }

    /**
     * 当前用户是唯一的
     *
     * @var bool
     */
    protected static $createNewInstance = false;

    protected $configs = [
        'enablePinCode' => [
            'default' => false,
        ],
        'checkMobileUnique' => [
            'default' => false,
        ],
        'defaultAvatar' => [
            'default' => '/images/head.jpg',
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
     * @var array
     */
    protected $requiredServices = [
        'db',
        'cache',
        'logger',
        'ret',
        'str',
    ];

    /**
     * @var string
     */
    protected $authClass = JwtAuth::class;

    /**
     * @var BaseAuth
     */
    protected $auth;

    /**
     * NOTE: 暂时只有__set有效
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value = null)
    {
        // __set start
        // Required services first
        if (in_array($name, $this->requiredServices, true)) {
            return $this->{$name} = $value;
        }

        // NOTE: 设置前需主动加载，否则状态变为loaded，不会再去加载
        $this->loadDbUser();

        $result = $this->set($name, $value, false);
        if ($result) {
            return;
        }

        if ($this->wei->has($name)) {
            return $this->{$name} = $value;
        }

        throw new \InvalidArgumentException('Invalid property: ' . $name);
        // __set end
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($returnFields = [], callable $prepend = null)
    {
        $this->loadDbUser();

        return parent::toArray($returnFields, $prepend);
    }

    /**
     * {@inheritdoc}
     */
    public function save($data = [])
    {
        // 确保是更新操作,同时有ID作为更新条件
        $this->isNew = false;
        $this['id'] = $this->getAuth()->getData()['id'];

        return parent::save($data);
    }

    /**
     * Record: 获取用户资料,优先从认证服务中获取
     *
     * {@inheritdoc}
     */
    public function &get($name, &$exists = null, $throwException = true)
    {
        // 未加载数据,已登录,认证服务中存在需要的key
        $data = $this->getAuth()->getData();
        if (!$this->isLoaded() && isset($data[$name])) {
            $exists = true;
            return $data[$name];
        } else {
            $this->loadDbUser();

            return parent::get($name, $exists, $throwException);
        }
    }

    public function &__get($name)
    {
        $result = $this->getConfig($name);
        return $result;
    }

    /**
     * 从数据库中查找用户加载到当前记录中
     */
    protected function loadDbUser()
    {
        if ($this->isLoaded() || !$this->isLogin()) {
            return;
        }

        $id = $this['id'];
        $user = wei()->userModel()
            ->cache()
            ->tags(false)
            ->setCacheKey($this->getRecordCacheKey($id))
            ->findOrInit($id);

        $this->loadRecordData($user);
    }

    /**
     * 加载外部记录的数据
     *
     * @param UserModel $user
     */
    protected function loadRecordData(UserModel $user)
    {
        $this->setData($user->getData());

        // 清空更改状态
        $this->isChanged = false;
        $this->changedData = [];
    }

    /**
     * @return int|null
     * @svc
     */
    protected function id()
    {
        return $this->id;
    }

    /**
     * @return UserModel
     * @svc
     */
    protected function cur()
    {
        return $this;
    }

    /**
     * 判断用户是否登录
     *
     * @return bool
     * @svc
     */
    protected function isLogin()
    {
        return $this->getAuth()->isLogin();
    }

    /**
     * 检查用户是否登录
     *
     * @return Ret
     * @svc
     */
    protected function checkLogin()
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
    protected function login($data)
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
        $user = wei()->userModel();
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
        return $this->loginByModel($user);
    }

    /**
     * 根据用户ID直接登录用户
     *
     * @param int $id
     * @return Ret
     * @svc
     */
    protected function loginById($id)
    {
        $user = wei()->userModel()->find($id);
        if (!$user) {
            return err('用户不存在');
        } else {
            return $this->loginByModel($user);
        }
    }

    /**
     * 根据条件查找或创建用户,并登录
     *
     * @param mixed $conditions
     * @param array $data
     * @return $this
     * @svc
     */
    protected function loginBy($conditions, $data = [])
    {
        $user = wei()->userModel()->findOrCreate($conditions, $data);
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
    protected function loginByModel(UserModel $user)
    {
        $this->loadRecordData($user);

        $ret = $this->getAuth()->login($user);
        if ($ret->isSuc()) {
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
    protected function logout()
    {
        $this->data = [];

        $this->getAuth()->logout();

        return suc();
    }

    /**
     * 当用户信息更改后,可以主动调用该方法,刷新会话中的数据
     *
     * @param UserModel $user
     * @return $this
     * @svc
     */
    protected function refresh(UserModel $user)
    {
        if ($user->id === $this->getAuth()->getData()['id']) {
            $this->loginByModel($user);
        }

        return $this;
    }

    protected function getAuth()
    {
        if (!$this->auth) {
            $this->auth = new $this->authClass;
        }
        return $this->auth;
    }
}
