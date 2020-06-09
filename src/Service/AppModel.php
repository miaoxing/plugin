<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\ConstTrait;
use Miaoxing\Plugin\Metadata\AppTrait;

/**
 * 应用模型
 */
class AppModel extends Model
{
    use ConstTrait;
    use AppTrait;

    const STATUS_ALL = 0;

    const STATUS_ONLINE = 1;

    const STATUS_OFFLINE = 2;

    protected $defaultCasts = [
        'plugin_ids' => 'array',
        'configs' => 'json',
    ];

    /**
     * @var array
     */
    protected $statusNames = [
        self::STATUS_ALL => '全部',
        self::STATUS_ONLINE => '正常',
        self::STATUS_OFFLINE => '下线',
    ];

    protected $data = [
        'configs' => [],
        'plugin_ids' => [],
    ];

    public function afterSave()
    {
        parent::afterSave();

        $this->cache->remove('appName:' . $this['name']);
    }
}
