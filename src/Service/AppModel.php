<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Metadata\AppTrait;
use Miaoxing\Services\ConstTrait;

/**
 * 应用模型
 *
 * @mixin \CacheMixin
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

    /**
     * 预先定义的应用slug,可以减少查询
     *
     * @var array
     */
    protected $predefinedNames = ['app'];

    /**
     * Repo: 根据名称判断应用是否存在
     *
     * @param string $name
     * @return bool
     */
    public function isExists($name)
    {
        // 忽略非数字和字母组成的项目名称
        if (!ctype_alnum($name)) {
            return false;
        }

        if (in_array($name, $this->predefinedNames)) {
            return true;
        }

        return $this->cache->get('appExists:' . $name, 86400, function () use ($name) {
            $app = wei()->appModel()->select('name')->fetch('name', $name);

            return $app && $app['name'] === $name;
        });
    }

    /**
     * Repo: 根据域名查找应用名称
     *
     * @param string $domain
     * @return string|false
     */
    public function getIdByDomain($domain)
    {
        return $this->cache->get('appDomain:' . $domain, 86400, function () use ($domain) {
            $app = wei()->appModel()->select('name')->fetch('domain', $domain);

            return $app ? $app['name'] : false;
        });
    }

    public function afterSave()
    {
        parent::afterSave();

        $this->cache->remove('appName:' . $this['name']);
    }
}
