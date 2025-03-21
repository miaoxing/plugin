<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\ConstTrait;
use Miaoxing\Plugin\Model\HasAppIdTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Miaoxing\Plugin\Model\ReqQueryTrait;
use Wei\Model\SoftDeleteTrait;

/**
 * 配置模型
 *
 * @property int|null $id
 * @property string $appId 应用编号
 * @property string $name
 * @property string $type 值的类型,s:字符串
 * @property string $value
 * @property string $comment
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property string $createdBy
 * @property string $updatedBy
 * @property string|null $deletedAt
 * @property string $deletedBy
 */
class ConfigModel extends BaseModel
{
    use ConstTrait;
    use HasAppIdTrait;
    use ModelTrait;
    use ReqQueryTrait;
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

    protected $virtual = [
        'typeName',
    ];

    /**
     * @var array
     */
    protected $typeNames = [
        self::TYPE_STRING => '字符串',
        self::TYPE_BOOL => '布尔值',
        self::TYPE_INT => '整数',
        self::TYPE_FLOAT => '小数',
        self::TYPE_NULL => 'NULL',
        self::TYPE_ARRAY => '数组',
        self::TYPE_JSON => 'JSON',
        self::TYPE_OBJECT => '对象',
        self::TYPE_EXPRESS => '表达式',
    ];

    /**
     * 类型名称
     *
     * @return string|null
     */
    protected function getTypeNameAttribute(): ?string
    {
        return $this->typeNames[$this->type] ?? null;
    }
}
