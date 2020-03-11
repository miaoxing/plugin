<?php

namespace Miaoxing\Plugin\Service;

/**
 * 当前用户
 */
class CurUser extends User
{
    /**
     * @var array
     */
    protected $requiredServices = [
        'db',
        'cache',
        'logger',
        'ret',
        'str',
        'session',
    ];

    /**
     * 缓存在session中的字段
     *
     * @var array
     */
    protected $sessionFields = ['id', 'username', 'nickName'];

    /**
     * 判断用户是否登录
     *
     * @return bool
     */
    public function isLogin()
    {
        return (bool) $this->session['user'];
    }

    /**
     * 根据用户账号密码,登录用户
     *
     * @param mixed $data
     * @return array
     */
    public function login($data)
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
            return ['code' => -1, 'message' => $validator->getFirstMessage()];
        }

        // 2. 检查手机/邮箱/用户名是否存在
        $user = wei()->user();
        switch (true) {
            case wei()->isMobileCn($data['username']):
                $field = 'mobile';
                $user->mobileVerified();
                break;

            case wei()->isEmail($data['username']):
                $field = 'email';
                break;

            default:
                $field = 'username';
        }

        /** @var User $user */
        $user = $user->find([$field => $data['username']]);
        if (!$user) {
            return ['code' => -2, 'message' => '用户名不存在或密码错误'];
        }

        // 3. 检查用户是否有效
        if (!$user['enable']) {
            return ['code' => -3, 'message' => '用户未启用,无法登录'];
        }

        // 4. 验证密码是否正确
        if (!$user->verifyPassword($data['password'])) {
            return ['code' => -4, 'message' => '用户不存在或密码错误'];
        }

        // 5. 验证通过,登录用户
        return $this->loginByRecord($user);
    }

    /**
     * 根据用户ID直接登录用户
     *
     * @param int $id
     * @return array
     */
    public function loginById($id)
    {
        /** @var User $user */
        $user = wei()->user()->findById($id);
        if (!$user) {
            return ['code' => -1, 'message' => '用户不存在'];
        } else {
            return $this->loginByRecord($user);
        }
    }

    /**
     * 根据条件查找或创建用户,并登录
     *
     * @param mixed $conditions
     * @param array $data
     * @return $this
     */
    public function loginBy($conditions, $data = [])
    {
        $user = wei()->user()->findOrCreate($conditions, $data);
        $this->loginByRecord($user);

        return $this;
    }

    /**
     * 根据用户对象登录用户
     *
     * @param User $user
     * @return array
     */
    public function loginByRecord(User $user)
    {
        $this->loadRecordData($user);
        $this->session['user'] = $user->toArray($this->sessionFields);
        wei()->event->trigger('userLogin', [$user]);

        return ['code' => 1, 'message' => '登录成功'];
    }

    /**
     * 销毁用户会话,退出登录
     *
     * @return $this
     */
    public function logout()
    {
        $this->data = [];
        unset($this->session['user']);

        return $this;
    }

    /**
     * 获取存储在session中的用户数据
     *
     * @return null|array
     */
    public function getSessionData()
    {
        return $this->session['user'];
    }

    /**
     * 当用户信息更改后,可以主动调用该方法,刷新会话中的数据
     *
     * @param User $user
     * @return $this
     */
    public function refresh(User $user)
    {
        if ($user['id'] == $this->session['user']['id']) {
            $this->loginByRecord($user);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($returnFields = [])
    {
        $this->loadDbUser();

        return parent::toArray($returnFields);
    }

    /**
     * {@inheritdoc}
     */
    public function save($data = [])
    {
        // 确保是更新操作,同时有ID作为更新条件
        $this->isNew = false;
        $this['id'] = $this->session['user']['id'];

        return parent::save($data);
    }

    /**
     * Record: 获取用户资料,优先从session中获取
     *
     * {@inheritdoc}
     */
    public function get($name)
    {
        // 未加载数据,已登录,session中存在需要的key
        if (!$this->isLoaded() && isset($this->session['user'][$name])) {
            return $this->session['user'][$name];
        } else {
            $this->loadDbUser();

            return parent::get($name);
        }
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
        $user = wei()->user()
            ->cache()
            ->tags(false)
            ->setCacheKey($this->getRecordCacheKey($id))
            ->findOrInitById($id);

        $this->loadRecordData($user);
    }

    /**
     * 加载外部记录的数据
     *
     * @param User $user
     */
    protected function loadRecordData(User $user)
    {
        $this->setData($user->getData());

        // 清空更改状态
        $this->isChanged = false;
        $this->changedData = [];
    }
}
