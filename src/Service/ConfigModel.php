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

    public const TYPE_STRING = 's';

    public const TYPE_BOOL = 'b';

    public const TYPE_INT = 'i';

    public const TYPE_FLOAT = 'f';

    public const TYPE_NULL = 'n';

    public const TYPE_ARRAY = 'a';

    public const TYPE_OBJECT = 'o';

    public const TYPE_JSON = 'j';

    public const TYPE_EXPRESS = 'e';

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
