<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Metadata\UserTrait;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Plugin\Model\SnowflakeTrait;
use Wei\Model\CacheTrait;
use Wei\Password;
use Wei\V;

/**
 * @mixin \UserMixin
 * @mixin \AppMixin
 */
class UserModel extends BaseModel
{
    use CacheTrait;
    use HasAppIdTrait;
    use ModelTrait;
    use ReqQueryTrait;
    use SnowflakeTrait;
    use UserTrait;

    protected $hidden = [
        'password',
    ];

    protected $virtual = [
        'displayName',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'sex' => 1,
    ];

    public function getGuarded()
    {
        return array_merge($this->guarded, [
            'isAdmin',
            'mobileVerifiedAt',
            'username',
            'password',
            'lastLoginAt',
        ]);
    }

    /**
     * 设置未加密的密码
     *
     * @param string $password
     * @return $this
     */
    public function setPlainPassword($password)
    {
        $this->password = Password::hash($password);

        return $this;
    }

    /**
     * 验证密码是否正确
     *
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password)
    {
        return Password::verify($password, $this->password);
    }

    /**
     * @return string|null
     */
    public function getDisplayNameAttribute()
    {
        foreach (['nickName', 'username', 'name'] as $column) {
            if ($name = $this[$column]) {
                return $name;
            }
        }
        return null;
    }

    /**
     * Model: 判断用户是否为超级管理员
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return '1' === $this->id;
    }

    /**
     * 通过外部检查用户是否有某个权限
     *
     * @param string $permissionId
     * @return bool
     * @svc
     */
    protected function can($permissionId)
    {
        $result = $this->getEventService()->until('userCan', [$permissionId, $this]);
        if (null === $result) {
            $result = true;
        }

        return (bool) $result;
    }

    /**
     * @param array|\ArrayAccess $req
     * @return \Wei\Ret
     * @svc
     */
    protected function updatePassword($req)
    {
        // 1. 校验
        $v = V::new();
        $v->string('oldPassword', '旧密码');
        $v->string('password', '新密码')->when(wei()->user->enablePinCode, static function (V $v) {
            $v->digit()->length(6);
        }, static function (V $v) {
            $v->minLength(6);
        });
        $v->string('passwordConfirm', '重复密码')->equalTo($req['password'])->message('equalTo', '两次输入的密码不相等');
        $ret = $v->check($req);
        if ($ret->isErr()) {
            return $ret;
        }

        // 2. 验证旧密码
        if ($this['password'] && !$this->verifyPassword($req['oldPassword'])) {
            return err('旧密码错误！请重新输入');
        }

        // 3. 更新新密码
        if (!$this->app->isDemo()) {
            $this->setPlainPassword($req['password']);
            $this->save();
        }

        User::logout();

        return suc();
    }

    protected function afterDestroy()
    {
        $this->removeModelCache();
    }

    protected function afterSave()
    {
        $this->removeModelCache();
        $this->user->refresh($this);
    }
}
