<?php

namespace MiaoxingTest\Plugin\Fixture;

use Miaoxing\Plugin\ConstTrait;

/**
 * 性别常量服务
 */
class SexConst
{
    use ConstTrait;

    const SEX_MALE = 1;

    const SEX_FEMALE = 2;

    protected $sexNames = [
        self::SEX_MALE => '男',
        self::SEX_FEMALE => '女',
    ];
}
