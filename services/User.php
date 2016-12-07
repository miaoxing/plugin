<?php

namespace miaoxing\plugin\services;

use miaoxing\plugin\BaseModel;
use plugins\user\services\Group;
use plugins\user\services\UserProfile;

/**
 * @property \Wei\Session $session
 * @property \Wei\Request $request
 * @property \Wei\Password $password
 * @property \Wei\Event $event
 */
class User extends BaseModel
{
    /**
     * 手机号码是否已验证
     */
    const STATUS_MOBILE_VERIFIED = 1;

    /**
     * 省市是否锁定(第三方平台不可更改)
     */
    const STATUS_REGION_LOCKED = 3;

    /**
     * {@inheritdoc}
     */
    protected $table = 'user';

    /**
     * {@inheritdoc}
     */
    protected $fullTable = 'user';

    /**
     * @var array
     */
    protected $data = [
        'gender' => 1,
    ];

    /**
     * @var Group
     */
    protected $group;

    /**
     * @var UserProfile
     */
    protected $profile;

    /**
     * 当前记录是否为新创建的
     *
     * @var bool
     */
    protected $isCreated = false;

    /**
     * QueryBuilder:
     *
     * @return $this
     */
    public function valid()
    {
        return $this->andWhere(['isValid' => 1]);
    }

    public function getProfile()
    {
        $this->profile || $this->profile = wei()->userProfile()->findOrInit(['userId' => $this['id']]);

        return $this->profile;
    }

    /**
     * Record: 根据条件查找或创建用户
     *
     * @param mixed $conditions
     * @param array $data
     * @return $this
     */
    public function findOrCreate($conditions, $data = [])
    {
        $this->findOrInit($conditions, $data);
        if ($this->isNew()) {
            $this->save();
        }

        return $this;
    }

    /**
     * Record: 获取用户的分组对象
     *
     * @return Group
     */
    public function getGroup()
    {
        $this->group || $this->group = wei()->group()->findOrInitById($this['groupId'], ['name' => '未分组']);

        return $this->group;
    }

    /**
     * Record: 验证密码是否正确
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return $this->password->verify($password, $this['password']);
    }

    /**
     * Record: 获取昵称等可供展示的名称
     *
     * @return string
     */
    public function getNickName()
    {
        foreach (['nickName', 'username', 'name'] as $name) {
            if ($this[$name]) {
                return $this[$name];
            }
        }

        return $this['isValid'] ? '游客' : '';
    }

    /**
     * Record: 设置未加密的密码
     *
     * @param string $password
     * @return $this
     */
    public function setPlainPassword($password)
    {
        $this['salt'] || $this['salt'] = $this->password->generateSalt();
        $this['password'] = $this->password->hash($password, $this['salt']);

        return $this;
    }

    /**
     * Record: 指定用户是否为管理员
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this['admin'];
    }

    /**
     * Record: 判断用户是否为超级管理员
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this['username'] == 'miaostar';
    }

    /**
     * Record: 获取用户头像,没有设置头像则使用默认头像
     *
     * @return string
     */
    public function getHeadImg()
    {
        return $this['headImg'] ?: '/assets/images/head/default-light.jpg';
    }

    /**
     * Repo: 根据用户编号,从缓存中获取用户名
     *
     * @param int $id
     * @return string
     */
    public function getDisplayNameByIdFromCache($id)
    {
        return wei()->arrayCache->get('nickName' . $id, function () use ($id) {
            $user = wei()->user()->find(['id' => $id]);

            return $user ? $user->getNickName() : '';
        });
    }

    /**
     * Record: 创建一个新用户
     *
     * wei()->user()->register([
     *     'email' => 'xx', // 可选
     *     'username' => 'xx',
     *     'password' => 'xx',
     *     'passwordAgain' => 'xx,
     *     'source' => 1, // 来源,可选
     * ]);
     *
     * @param array $data
     * @return array
     * @todo 太多validate,需简化
     */
    public function register($data)
    {
        // 1. 校验额外数据
        if (isset($data['mobile'])) {
            $validator = wei()->validate([
                'data' => $data,
                'rules' => [
                    'mobile' => [
                        'required' => true,
                        'mobileCn' => true,
                    ],
                    'verifyCode' => [
                        'required' => true,
                    ],
                ],
                'names' => [
                    'mobile' => '手机号码',
                    'verifyCode' => '验证码',
                ],
                'messages' => [
                    'mobile' => [
                        'required' => '请输入手机号码',
                    ],
                    'verifyCode' => [
                        'required' => '请输入验证码',
                    ],
                ],
            ]);
            if (!$validator->isValid()) {
                return ['code' => -1, 'message' => $validator->getFirstMessage()];
            }

            $ret = wei()->verifyCode->check($data['mobile'], $data['verifyCode']);
            if ($ret['code'] !== 1) {
                return $ret + ['verifyCodeErr' => true];
            }
        } else {
            $validator = wei()->validate([
                'data' => $data,
                'rules' => [
                    'email' => [
                        'required' => true,
                    ],
                ],
                'names' => [
                    'email' => '邮箱',
                ],
                'messages' => [
                    'email' => [
                        'required' => '请输入邮箱',
                    ],
                ],
            ]);
            if (!$validator->isValid()) {
                return ['code' => -1, 'message' => $validator->getFirstMessage()];
            }
        }

        // 2. 统一校验
        $validator = wei()->validate([
            'data' => $data,
            'rules' => [
                'email' => [
                    'required' => false,
                    'email' => true,
                    'notRecordExists' => ['user', 'email'],
                ],
                'password' => [
                    'minLength' => 6,
                ],
                'passwordConfirm' => [
                    'equalTo' => $data['password'],
                ],
            ],
            'names' => [
                'email' => '邮箱',
                'password' => '密码',
            ],
            'messages' => [
                'passwordConfirm' => [
                    'required' => '请再次输入密码',
                    'equalTo' => '两次输入的密码不相等',
                ],
            ],
        ]);
        if (!$validator->isValid()) {
            return ['code' => -7, 'message' => $validator->getFirstMessage()];
        }

        if ($data['mobile']) {
            $user = wei()->user()->withStatus(self::STATUS_MOBILE_VERIFIED)->find(['mobile' => $data['mobile']]);
            if ($user) {
                return ['code' => -8, 'message' => '手机号码已存在'];
            }
        }

        $ret = $this->event->until('userRegisterValidate', [$this]);
        if ($ret) {
            return $ret;
        }

        // 3. 保存到数据库
        $this->setPlainPassword($data['password']);

        if ($data['mobile']) {
            $this->setStatus(static::STATUS_MOBILE_VERIFIED, true);
        }

        $this->save([
            'email' => (string) $data['email'],
            'mobile' => (string) $data['mobile'],
            'username' => (string) $data['username'],
            'source' => isset($data['source']) ? $data['source'] : 0,
        ]);

        return ['code' => 1, 'message' => '注册成功'];
    }

    /**
     * Repo: 记录用户操作日志
     *
     * @param string $action
     * @param array $data
     * @return $this
     */
    public function log($action, array $data)
    {
        $curUser = wei()->curUser;
        $app = wei()->app;

        if (isset($data['param']) && is_array($data['param'])) {
            $data['param'] = json_encode($data['param'], JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['ret']) && is_array($data['ret'])) {
            $data['ret'] = json_encode($data['ret'], JSON_UNESCAPED_UNICODE);
        }

        wei()->appDb->insert('userLogs', $data + [
                'appId' => $app->getId(),
                'userId' => (int) $curUser['id'],
                'nickName' => $curUser->getNickName(),
                'page' => $app->getControllerAction(),
                'action' => $action,
                'createTime' => date('Y-m-d H:i:s'),
            ]);

        return $this;
    }

    public function afterCreate()
    {
        parent::afterCreate();

        $this->isCreated = true;

        // TODO 移到插件中
        if ($this['wechatOpenId']) {
            $this->db->insert('wechatSyncUsers', ['id' => $this['id']]);
        }
    }

    public function afterSave()
    {
        parent::afterSave();
        wei()->curUser->refresh($this);
        $this->clearRecordCache();
    }

    public function afterDestroy()
    {
        parent::afterDestroy();
        $this->clearRecordCache();
    }

    /**
     * Record: 移动用户分组
     *
     * @param int $groupId
     * @return array
     */
    public function updateGroup($groupId)
    {
        $group = wei()->group()->findOrInitById($groupId);
        $ret = wei()->event->until('groupMove', [[$this['id']], $group]);
        if ($ret) {
            return $ret;
        }

        $this->save(['groupId' => $groupId]);

        return ['code' => 1, 'message' => '操作成功'];
    }

    /**
     * 设置某个状态为是否开启
     *
     * @param int $position
     * @param bool $value
     * @return $this
     */
    public function setStatus($position, $value)
    {
        $status = pow(2, $position - 1);
        if ($value) {
            $status = $this['status'] | $status;
        } elseif ($this['status'] !== null) {
            $status = $this['status'] & ~$status;
        } else {
            $status = ~$status;
        }
        $this['status'] = $status & 0xFFFF;

        return $this;
    }

    /**
     * 检查某个状态位是否开启
     *
     * @param int $position
     * @return bool
     */
    public function isStatus($position)
    {
        return (bool) ($this['status'] & pow(2, $position - 1));
    }

    /**
     * QueryBuilder: 查询某个状态是启用的记录
     *
     * @param int $position
     * @return $this
     */
    public function withStatus($position)
    {
        $value = pow(2, $position - 1);

        return $this->andWhere("status & $value = $value");
    }

    /**
     * QueryBuilder: 查询某个状态是禁用的记录
     *
     * @param int $position
     * @return $this
     */
    public function withoutStatus($position)
    {
        $value = pow(2, $position - 1);

        return $this->andWhere("status & $value = 0");
    }

    /**
     * Record: 检查当前记录是否刚创建
     *
     * @return bool
     */
    public function isCreated()
    {
        return $this->isCreated;
    }

    /**
     * Record: 检查指定的手机号码能否绑定当前用户
     *
     * @param string $mobile
     * @return array
     */
    public function checkMobile($mobile)
    {
        // 1. 检查是否已存在认证该手机号码的用户
        $mobileUser = wei()->user()->withStatus(self::STATUS_MOBILE_VERIFIED)->find(['mobile' => $mobile]);
        if ($mobileUser && $mobileUser['id'] != $this['id']) {
            return $this->err('已存在认证该手机号码的用户');
        }

        // 2. 提供接口供外部检查手机号
        $ret = $this->event->until('userCheckMobile', [$this, $mobile]);
        if ($ret) {
            return $ret;
        }

        return $this->suc('手机号码可以绑定');
    }

    /**
     * Record: 绑定手机
     *
     * @param array|\ArrayAccess $data
     * @return array
     */
    public function bindMobile($data)
    {
        // 1. 校验数据
        $ret = $this->checkMobile($data['mobile']);
        if ($ret['code'] !== 1) {
            return $ret;
        }

        // 2. 校验验证码
        $ret = wei()->verifyCode->check($data['mobile'], $data['verifyCode']);
        if ($ret['code'] !== 1) {
            return $ret + ['verifyCodeErr' => true];
        }

        // 3. 记录手机信息
        $this['mobile'] = $data['mobile'];
        $this->setStatus(self::STATUS_MOBILE_VERIFIED, true);

        $this->event->trigger('preUserMobileVerify', [$data, $this]);

        $this->save();

        return $this->suc('绑定成功');
    }

    /**
     * Record: 更新当前用户资料
     *
     * @param array|\ArrayAccess $data
     * @return array
     */
    public function updateData($data)
    {
        $isMobileVerified = $this->isStatus(static::STATUS_MOBILE_VERIFIED);

        $validator = wei()->validate([
            'data' => $data,
            'rules' => [
                'mobile' => [
                    'required' => !$isMobileVerified,
                    'mobileCn' => true,
                ],
                'name' => [
                    'required' => false,
                ],
                'address' => [
                    'required' => false,
                    'minLength' => 3,
                ],
            ],
            'names' => [
                'mobile' => '手机号码',
                'name' => '姓名',
                'address' => '详细地址',
            ],
        ]);
        if (!$validator->isValid()) {
            return $this->err($validator->getFirstMessage());
        }

        if (!$isMobileVerified) {
            $this['mobile'] = $data['mobile'];
        }

        $result = $this->event->until('preUserUpdate', [$data, $this]);
        if ($result) {
            return $result;
        }

        $this->save([
            'name' => $data['name'],
            'address' => $data['address'],
        ]);

        return $this->suc();
    }

    /**
     * @param array|\ArrayAccess $req
     * @return array
     */
    public function updatePassword($req)
    {
        // 1. 校验
        $validator = wei()->validate([
            'data' => $req,
            'rules' => [
                'oldPassword' => [
                ],
                'password' => [
                    'minLength' => 6,
                ],
                'passwordConfirm' => [
                    'equalTo' => $req['password'],
                ],
            ],
            'names' => [
                'oldPassword' => '旧密码',
                'password' => '新密码',
                'passwordConfirm' => '重复密码',
            ],
            'messages' => [
                'passwordConfirm' => [
                    'equalTo' => '两次输入的密码不相等',
                ],
            ],
        ]);
        if (!$validator->isValid()) {
            return $this->err($validator->getFirstMessage());
        }

        // 2. 验证旧密码
        if ($this['password'] && $this['salt']) {
            $isSuc = $this->verifyPassword($req['oldPassword']);
            if (!$isSuc) {
                return $this->err('旧密码输入错误！请重新输入');
            }
        }

        // 3. 更新新密码
        $this->setPlainPassword($req['password']);
        $this->save();

        return $this->suc();
    }
}
