<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\ConstTrait;
use Miaoxing\Plugin\Model\ModelTrait;
use Wei\Model\CacheTrait;

/**
 * 应用模型
 *
 * @property array $pluginIds 已安装的插件编号
 * @property string|null $id
 * @property int $userId 所属用户的编号
 * @property string $name 名称
 * @property string $domain 绑定的域名
 * @property string $description 描述
 * @property int $status 状态
 * @property string|null $createdAt
 * @property string|null $updatedAt
 * @property string $createdBy
 * @property string $updatedBy
 */
class AppModel extends BaseModel
{
    use CacheTrait;
    use ConstTrait;
    use ModelTrait;

    public const STATUS_ALL = 0;

    public const STATUS_ONLINE = 1;

    public const STATUS_OFFLINE = 2;

    /**
     * @var array
     */
    protected $statusNames = [
        self::STATUS_ALL => '全部',
        self::STATUS_ONLINE => '正常',
        self::STATUS_OFFLINE => '下线',
    ];

    protected $columns = [
        'pluginIds' => [
            'cast' => 'list',
        ],
    ];

    public function afterSave()
    {
        $this->removeModelCache();
    }
}
