<?php

namespace Miaoxing\Plugin\Service;

/**
 * 应用模型
 *
 * @property \Wei\BaseCache $cache
 */
class AppRecord extends \miaoxing\plugin\BaseModel
{
    use \Miaoxing\Plugin\Constant;

    const STATUS_ALL = 0;

    const STATUS_ONLINE = 1;

    const STATUS_OFFLINE = 2;

    /**
     * @var array
     */
    protected $statusTable = [
        self::STATUS_ALL => [
            'text' => '全部',
        ],
        self::STATUS_ONLINE => [
            'text' => '正常',
        ],
        self::STATUS_OFFLINE => [
            'text' => '下线',
        ],
    ];

    protected $table = 'apps';

    protected $providers = [
        'db' => 'app.db',
    ];

    protected $data = [
        'configs' => [],
        'pluginIds' => [],
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

        return $this->cache->get('appExists' . $name, 86400, function () use ($name) {
            $app = wei()->appRecord()->select('name')->fetch(['name' => $name]);

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
        return $this->cache->get('appDomain' . $domain, 86400, function () use ($domain) {
            $app = wei()->appRecord()->select('name')->fetch(['domain' => $domain]);

            return $app ? $app['name'] : false;
        });
    }

    public function afterFind()
    {
        parent::afterFind();
        $this['configs'] = (array) json_decode($this['configs'], true);
        $this['pluginIds'] = explode(',', $this['pluginIds']);
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $this['configs'] = json_encode((array) $this['configs']);
        $this['pluginIds'] = implode(',', $this['pluginIds']);
    }

    public function afterSave()
    {
        parent::afterSave();

        $this->afterFind();

        $this->cache->remove('appName:' . $this['name']);
    }

    /**
     * 获取应用的配置
     *
     * @param string $name
     * @return null|string
     */
    public function getConfig($name)
    {
        return isset($this['configs'][$name]) ? $this['configs'][$name] : null;
    }
}
