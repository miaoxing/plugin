<?php

namespace Miaoxing\Plugin\Metadata;

use Miaoxing\Plugin\Model\ModelTrait;

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
 * @property string|null $mobileVerifiedAt 手机校验时间
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
 * @property string|null $lastLoginAt
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property int $createdBy
 * @property int $updatedBy
 * @property int $score 积分
 * @property float $money 账户余额
 * @property float $rechargeMoney 充值账户余额
 * @property bool $isSubscribed 是否关注
 * @property string|null $subscribedAt 关注时间
 * @property string|null $unsubscribedAt 取关时间
 * @property string $source 用户来源
 * @property mixed $isMobileVerified
 * @property string|null $displayName
 * @internal will change in the future
 */
trait UserTrait
{
    use ModelTrait;

    /**
     * @var array
     * @see CastTrait::$casts
     */
    protected $casts = [
        'id' => 'int',
        'appId' => 'int',
        'outId' => 'string',
        'groupId' => 'int',
        'wechatOpenId' => 'string',
        'wechatUnionId' => 'string',
        'isAdmin' => 'bool',
        'nickName' => 'string',
        'remarkName' => 'string',
        'username' => 'string',
        'name' => 'string',
        'email' => 'string',
        'mobile' => 'string',
        'mobileVerifiedAt' => 'datetime',
        'phone' => 'string',
        'password' => 'string',
        'sex' => 'int',
        'country' => 'string',
        'province' => 'string',
        'city' => 'string',
        'area' => 'string',
        'address' => 'string',
        'signature' => 'string',
        'isEnabled' => 'bool',
        'avatar' => 'string',
        'lastLoginAt' => 'datetime',
        'createdAt' => 'datetime',
        'updatedAt' => 'datetime',
        'createdBy' => 'int',
        'updatedBy' => 'int',
        'score' => 'int',
        'money' => 'float',
        'rechargeMoney' => 'float',
        'isSubscribed' => 'bool',
        'subscribedAt' => 'datetime',
        'unsubscribedAt' => 'datetime',
        'source' => 'string',
    ];
}
