<?php

namespace Miaoxing\Plugin\Seeder;

use Faker\Factory;
use Miaoxing\Plugin\Service\UserModel;

class V20210414210514CreateUsers extends BaseSeeder
{
    protected const SEX_MALE = 1;

    protected const SEX_FEMALE = 2;

    protected $regions = [
        '中国 山东 青岛',
        '中国 广东 广州',
        '中国 江苏 南通',
        '中国 广西 柳州',
        '中国 浙江 温州',
        '中国 上海 浦东新区',
        '中国 上海 闵行',
        '中国',
        '中国 广东 深圳',
        '格陵兰',
        '中国 辽宁 铁岭',
        '中国 河南 三门峡',
        '希腊',
    ];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $faker = Factory::create('zh_CN');

        foreach (range(1, 30) as $i) {
            $regions = explode(' ', $faker->randomElement($this->regions));

            $sex = $faker->randomElement([static::SEX_MALE, static::SEX_FEMALE]);

            $firstName = static::SEX_MALE === $sex ? $faker->firstNameMale : $faker->firstNameFemale;

            /** @var \DateTime|null $mobileVerifiedAt */
            $mobileVerifiedAt = $faker->optional()->dateTimeThisYear();
            if ($mobileVerifiedAt) {
                $mobileVerifiedAt = $mobileVerifiedAt->format('Y-m-d H:i:s');
            }

            UserModel::saveAttributes([
                'name' => $faker->lastName . $firstName,
                'username' => $faker->userName,
                'email' => $faker->email,
                'mobile' => $faker->phoneNumber,
                'mobileVerifiedAt' => $mobileVerifiedAt,
                'sex' => $sex,
                'country' => $regions[0],
                'province' => $regions[1] ?? '',
                'city' => $regions[2] ?? '',
                'avatar' => $faker->imageUrl(480),
            ]);
        }
    }
}
