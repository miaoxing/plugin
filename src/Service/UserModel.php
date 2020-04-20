<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Metadata\UserTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\Services\Service\V;

class UserModel extends Model
{
    use UserTrait;
    use ReqQueryTrait;

    protected $hidden = [
        'salt',
        'password',
    ];

    protected $virtual = [
        'display_name',
    ];

    /**
     * @var array
     */
    protected $data = [
        'sex' => 1,
    ];

    /**
     * 设置未加密的密码
     *
     * @param string $password
     * @return $this
     */
    public function setPlainPassword($password)
    {
        $this['salt'] || $this['salt'] = wei()->password->generateSalt();
        $this['password'] = wei()->password->hash($password, $this['salt']);

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
        return password_verify($password, $this->password);
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
        $result = $this->event->until('userCan', [$permissionId, $this]);
        if ($result === null) {
            $result = true;
        }

        return (bool) $result;
    }

    public function getDisplayNameAttribute()
    {
        foreach (['nickName', 'username', 'name'] as $column) {
            if ($name = $this[$column]) {
                return $name;
            }
        }
    }

    /**
     * Model: 判断用户是否为超级管理员
     *
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->id === 1;
    }

    /**
     * @param array|\ArrayAccess $req
     * @return Ret
     * @svc
     */
    protected function updatePassword($req)
    {
        // 1. 校验
        $ret = V::key('oldPassword', '旧密码')
            ->key('password', '新密码')
            ->when(wei()->user->enablePinCode, static function (V $v) {
                $v->digit()->length(6);
            }, static function (V $v) {
                $v->minLength(6);
            })
            ->key('passwordConfirm', '重复密码')
            ->equalTo($req['password'])
            ->message('equalTo', '两次输入的密码不相等')
            ->check($req);
        if ($ret->isErr()) {
            return $ret;
        }

        // 2. 验证旧密码
        if ($this['password'] && $this['salt'] && !$this->verifyPassword($req['oldPassword'])) {
            return $this->err('旧密码错误！请重新输入');
        }

        // 3. 更新新密码
        $this->setPlainPassword($req['password']);
        $this->save();

        User::logout();

        return $this->suc();
    }
}
