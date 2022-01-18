<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\ConstTrait;
use Miaoxing\Plugin\Metadata\ConfigTrait;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\SoftDeleteTrait;

/**
 * 配置模型
 */
class ConfigModel extends BaseModel
{
    use ConfigTrait;
    use ConstTrait;
    use HasAppIdTrait;
    use ModelTrait;
    use SoftDeleteTrait;

    public const TYPE_STRING = 1;

    public const TYPE_BOOL = 2;

    public const TYPE_INT = 3;

    public const TYPE_FLOAT = 4;

    public const TYPE_NULL = 5;

    public const TYPE_JSON = 6;

    public const TYPE_EXPRESS = 7;

    /**
     * @var array
     */
    protected $typeNames = [
        self::TYPE_STRING => '字符串',
        self::TYPE_BOOL => '布尔值',
        self::TYPE_INT => '整数',
        self::TYPE_FLOAT => '小数',
        self::TYPE_NULL => 'NULL',
        self::TYPE_JSON => 'JSON',
        self::TYPE_EXPRESS => '表达式',
    ];
}
