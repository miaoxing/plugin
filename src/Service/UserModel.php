<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Metadata\UserTrait;
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

    public function group()
    {
        return $this->hasOne(wei()->groupModel(), 'id', 'groupId');
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
        return $this->where('mobileVerifiedAt', '!=', '0000-00-00 00:00:00');
    }

    /**
     * @param bool $verified
     * @return $this
     */
    public function setMobileVerified($verified = true)
    {
        $this->mobileVerifiedAt = $verified ? wei()->time() : '0000-00-00 00:00:00';
        return $this;
    }
}
