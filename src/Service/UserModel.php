<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Metadata\UserTrait;
use Miaoxing\Services\Service\Time;
use Miaoxing\User\Service\Group;
use Miaoxing\User\Service\GroupModel;

/**
 * @property GroupModel $group
 */
class UserModel extends Model
{
    use UserTrait;

    protected $hidden = [
        'salt',
        'password',
    ];

    protected $virtual = [
        'isMobileVerified',
    ];

    /**
     * 省市是否锁定(第三方平台不可更改)
     */
    const STATUS_REGION_LOCKED = 3;

    /**
     * @var array
     */
    protected $data = [
        'sex' => 1,
    ];

    /**
     * @var Group
     */
    protected $group;

    /**
     * @var \Miaoxing\User\Service\UserProfile
     */
    protected $profile;

    /**
     * 当前记录是否为新创建的
     *
     * @var bool
     */
    protected $isCreated = false;


    public function group()
    {
        return $this->hasOne(wei()->groupModel(), 'id', 'groupId');
    }

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
     * Record: 获取昵称等可供展示的名称
     *
     * @return string
     */
    public function getNickName()
    {
        foreach (['nickName', 'username', 'email', 'name'] as $name) {
            if ($this[$name]) {
                return $this[$name];
            }
        }

        return '';
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
     * @deprecated 使用 $this->admin
     */
    public function isAdmin()
    {
        return (bool) $this->admin;
    }

    /**
     * Record: 判断用户是否为超级管理员
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->id === 1 || $this['username'] == 'miaostar';
    }

    /**
     * Record: 获取用户头像,没有设置头像则使用默认头像
     *
     * @return string
     * @deprecated
     */
    public function getHeadImg()
    {
        return $this['headImg'];
    }

    public function getBackendDisplayName()
    {
        if ($this['name'] && $this['nickName']) {
            return $this['name'] . '(' . $this['nickName'] . ')';
        } elseif ($this['name']) {
            return $this['name'];
        } else {
            return $this['nickName'];
        }
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
            $user = wei()->user()->mobileVerified()->find(['mobile' => $data['mobile']]);
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
            $this->setMobileVerified();
        }

        $this->save([
            'email' => (string) $data['email'],
            'mobile' => (string) $data['mobile'],
            'username' => (string) $data['username'],
            'source' => isset($data['source']) ? $data['source'] : '',
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
        $user = User::cur();
        $app = wei()->app;

        if (isset($data['param']) && is_array($data['param'])) {
            $data['param'] = json_encode($data['param'], JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['ret']) && is_array($data['ret'])) {
            $data['ret'] = json_encode($data['ret'], JSON_UNESCAPED_UNICODE);
        }

        wei()->db->insert('userLogs', $data + [
                'appId' => $app->getId(),
                'userId' => $user->id,
                'nickName' => $user->nickName,
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

        /* TODO queue
        if (wei()->has('queue')) {
            wei()->queue->push(UserCreate::class, ['id' => $this['id']]);
        }*/

        // TODO 移到插件中
        if ($this['wechatOpenId']) {
            $this->db->insert('wechatSyncUsers', ['id' => $this['id']]);
        }
    }

    public function afterSave()
    {
        parent::afterSave();
        $this->user->refresh($this);
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

        return $this->suc();
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
     * @api
     */
    protected function checkMobile(string $mobile)
    {
        // 1. 检查是否已存在认证该手机号码的用户
        $mobileUser = wei()->userModel()->mobileVerified()->findBy('mobile', $mobile);
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
     * @api
     */
    protected function bindMobile($data)
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
        $this->setMobileVerified();

        $this->event->trigger('preUserMobileVerify', [$data, $this]);

        $this->save();

        return $this->suc('绑定成功');
    }

    /**
     * Record: 更新当前用户资料
     *
     * @param array|\ArrayAccess $data
     * @return array
     * @api
     */
    protected function updateData($data)
    {
        $isMobileVerified = $this->isMobileVerified();

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
            // 手机号未认证时,检查手机号,根据配置检查是否重复
            if (wei()->user->checkMobileUnique && $this->isMobileExists($data['mobile'])) {
                return $this->err('手机号码已存在');
            }
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

    public function isMobileExists($mobile)
    {
        return (bool) wei()->user()
            ->andWhere(['mobile' => $mobile])
            ->andWhere('id != ?', $this['id'])
            ->fetchColumn();
    }

    /**
     * @param array|\ArrayAccess $req
     * @return array
     * @api
     */
    protected function updatePassword($req)
    {
        if (wei()->user->enablePinCode) {
            $rule = [
                'digit' => true,
                'length' => 6,
            ];
        } else {
            $rule = [
                'minLength' => 6,
            ];
        }

        // 1. 校验
        $validator = wei()->validate([
            'data' => $req,
            'rules' => [
                'oldPassword' => [
                ],
                'password' => $rule,
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

        User::logout();

        return $this->suc();
    }

    /**
     * 通过外部检查用户是否有某个权限
     *
     * @param string $permissionId
     * @return bool
     * @api
     */
    protected function can($permissionId)
    {
        $result = $this->event->until('userCan', [$permissionId, $this]);
        if ($result === null) {
            $result = true;
        }

        return (bool) $result;
    }

    public function getTags()
    {
        $userTags = wei()->userTag->getAll();
        $tags = [];
        $relations = wei()->userTagsUserModel()->asc('id')->findAll(['user_id' => $this['id']]);
        foreach ($relations as $relation) {
            $tags[] = $userTags[$relation->tagId];
        }
        return $tags;
    }

    /**
     * Model: 验证密码是否正确
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return password_verify($password, $this->password);
    }

    /**
     * QueryBuilder: 查询手机号码验证过
     *
     * @return $this
     */
    public function mobileVerified()
    {
        return $this->where('mobile_verified_at', '!=', '0000-00-00 00:00:00');
    }

    /**
     * @param bool $verified
     * @return $this
     */
    public function setMobileVerified($verified = true)
    {
        $this->mobileVerifiedAt = $verified ? Time::now() : '0000-00-00 00:00:00';
        return $this;
    }

    public function updateMobileIfVerified($save = true, $req = null)
    {
        $req || $req = $this->request;

        // 未校验,或者是输入了新手机,需要校验
        if (!$this->isMobileVerified()
            || $this['mobile'] != $req['mobile']
        ) {
            $ret = $this->checkMobile($req['mobile']);
            if ($ret['code'] !== 1) {
                return $ret;
            }

            if (!$req['verifyCode']) {
                return $this->err('验证码不能为空');
            }

            $ret = wei()->verifyCode->check($req['mobile'], $req['verifyCode']);
            if ($ret['code'] !== 1) {
                return $ret + ['verifyCodeErr' => true];
            }
        }

        if ($this['mobile'] == $req['mobile']) {
            return $this->suc(['changed' => false]);
        }

        $this['mobile'] = $req['mobile'];
        $this->setMobileVerified();
        if ($save) {
            $this->save();
        }
        return $this->suc(['changed' => true]);
    }


    public function getIsMobileVerifiedAttribute()
    {
        return (bool) $this->mobileVerifiedAt;
    }

    public function setIsMobileVerifiedAttribute()
    {
        // do nothing
    }

    public function getAvatarAttribute()
    {
        return $this->data['avatar'] ?: $this->user->defaultAvatar;
    }
}
