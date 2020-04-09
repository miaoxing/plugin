<?php

namespace Miaoxing\Plugin\Metadata;

/**
 * UserTrait
 *
 * @property int $id
 * @property int $appId
 * @property string $outId
 * @property string $wechatOpenId 微信的OpenID
 * @property string $wechatUnionId
 * @property bool $admin
 * @property string $nickName
 * @property string $remarkName
 * @property string $username
 * @property string $name
 * @property string $email
 * @property string $mobile
 * @property string $mobileVerifiedAt 手机校验时间
 * @property string $phone
 * @property string $salt
 * @property string $password
 * @property int $sex
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $signature
 * @property string $avatar
 * @property int $groupId 用户组
 * @property float $money 账户余额
 * @property float $rechargeMoney 充值账户余额
 * @property int $score 积分
 * @property string $lastLoginAt
 * @property string $unsubscribedAt 取关时间
 * @property bool $isSubscribed
 * @property bool $enable 是否启用
 * @property string $source 用户来源
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * @property  $displayName
 */
trait UserTrait
{
    /**
     * @var array
     * @see CastTrait::$casts
     */
    protected $casts = [
        'id' => 'int',
        'app_id' => 'int',
        'out_id' => 'string',
        'wechat_open_id' => 'string',
        'wechat_union_id' => 'string',
        'admin' => 'bool',
        'nick_name' => 'string',
        'remark_name' => 'string',
        'username' => 'string',
        'name' => 'string',
        'email' => 'string',
        'mobile' => 'string',
        'mobile_verified_at' => 'datetime',
        'phone' => 'string',
        'salt' => 'string',
        'password' => 'string',
        'sex' => 'int',
        'country' => 'string',
        'province' => 'string',
        'city' => 'string',
        'area' => 'string',
        'address' => 'string',
        'signature' => 'string',
        'avatar' => 'string',
        'group_id' => 'int',
        'money' => 'float',
        'recharge_money' => 'float',
        'score' => 'int',
        'last_login_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'is_subscribed' => 'bool',
        'enable' => 'bool',
        'source' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_by' => 'int',
        'updated_by' => 'int',
    ];
}
