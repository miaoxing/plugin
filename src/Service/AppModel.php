<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\ConstTrait;
use Miaoxing\Plugin\Metadata\AppTrait;
use Miaoxing\Plugin\Model\ModelTrait;

/**
 * 应用模型
 *
 * @property array $pluginIds 已安装的插件编号
 */
class AppModel extends BaseModel
{
    use ModelTrait;
    use ConstTrait;
    use AppTrait;

    const STATUS_ALL = 0;

    const STATUS_ONLINE = 1;

    const STATUS_OFFLINE = 2;

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
        $this->cache->remove('appName:' . $this['name']);
    }
}
