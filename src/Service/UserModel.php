<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Metadata\UserTrait;

class UserModel extends Model
{
    use UserTrait;

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
        $this['salt'] || $this['salt'] = $this->password->generateSalt();
        $this['password'] = $this->password->hash($password, $this['salt']);

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

    public function getDisplayNameAttribute()
    {
        foreach (['nickName', 'username', 'name'] as $column) {
            if ($name = $this[$column]) {
                return $name;
            }
        }
    }
}
