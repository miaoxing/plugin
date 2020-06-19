<?php

namespace Miaoxing\Plugin\Metadata;

/**
 * UserTrait
 *
 * @property int $id
 * @property int $appId
 * @property string $outId
 * @property int $groupId 用户组
 * @property string $wechatOpenId 微信的OpenID
 * @property string $wechatUnionId
 * @property bool $isAdmin
 * @property string $nickName
 * @property string $remarkName
 * @property string $username
 * @property string $name
 * @property string $email
 * @property string $mobile
 * @property string $mobileVerifiedAt 手机校验时间
 * @property string $phone
 * @property string $password
 * @property int $sex
 * @property string $country
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $signature
 * @property bool $isEnabled 是否启用
 * @property string $avatar
 * @property string $lastLoginAt
 * @property string $createdAt
 * @property string $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * @property int $score 积分
 * @property float $money 账户余额
 * @property float $rechargeMoney 充值账户余额
 * @property bool $isSubscribed 是否关注
 * @property string $subscribedAt 关注时间
 * @property string $unsubscribedAt 取关时间
 * @property string $source 用户来源
 * @property bool $isMobileVerified
 * @property string|null $displayName
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
        'group_id' => 'int',
        'wechat_open_id' => 'string',
        'wechat_union_id' => 'string',
        'is_admin' => 'bool',
        'nick_name' => 'string',
        'remark_name' => 'string',
        'username' => 'string',
        'name' => 'string',
        'email' => 'string',
        'mobile' => 'string',
        'mobile_verified_at' => 'datetime',
        'phone' => 'string',
        'password' => 'string',
        'sex' => 'int',
        'country' => 'string',
        'province' => 'string',
        'city' => 'string',
        'area' => 'string',
        'address' => 'string',
        'signature' => 'string',
        'is_enabled' => 'bool',
        'avatar' => 'string',
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'created_by' => 'int',
        'updated_by' => 'int',
        'score' => 'int',
        'money' => 'float',
        'recharge_money' => 'float',
        'is_subscribed' => 'bool',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'source' => 'string',
    ];
}
