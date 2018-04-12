<?php

namespace Miaoxing\Plugin\Service;

/**
 * 设置
 *
 * @property \Wei\Cache $cache
 * @property \Wei\Validator\StartsWith $isStartsWith
 */
class Setting extends \Miaoxing\Plugin\BaseModel
{
    protected $table = 'setting';

    protected $labels = [
        'site' => '官网设置',
        'site.title' => '站点标题',
    ];

    protected $editorFields = [];

    /**
     * {@inheritdoc}
     */
    protected $providers = [
        'cache' => 'nearCache',
    ];

    /**
     * @param string $id
     * @param mixed $default
     * @return string|$this|$this[]
     */
    public function __invoke($id = null, $default = null)
    {
        if (func_num_args()) {
            // wei()->setting('id', 'default') 从缓存或数据库获取某项配置
            return $this->getValue($id, $default);
        } else {
            // wei()->setting() 初始化setting的Record对象
            return parent::__invoke();
        }
    }

    public function getLabel()
    {
        return $this->labels[$this['id']];
    }

    public function getTypeLabel()
    {
        // 不显示注释掉的配置
        if (!isset($this->labels[$this['id']])) {
            return '';
        }

        list($type) = explode('.', $this['id']);

        return $this->labels[$type];
    }

    /**
     * 保存之后,重建缓存
     */
    public function afterSave()
    {
        parent::afterSave();
        $this->cache->set($this->getRecordCacheKey(), $this['value'], 86400);
    }

    /**
     * Repo: 获取某项配置的值
     *
     * @param string $id
     * @param mixed $default
     * @return mixed
     */
    public function getValue($id, $default = null)
    {
        $value = $this->cache->get($this->getRecordCacheKey($id), 86400, function () use ($id, $default) {
            $setting = $this->db->select($this->table, ['id' => $id]);
            // 返回null而不是false,false意味着缓存失效,下次又重新查询
            return $setting ? $setting['value'] : null;
        });

        return $value === null ? $default : $value;
    }

    /**
     * Repo: 设置某项配置的值
     *
     * @param string $id
     * @param mixed $value
     * @return $this
     */
    public function setValue($id, $value)
    {
        $setting = parent::__invoke();
        $setting->findOrInitById($id);
        $setting->save(['value' => $value]);

        return $this;
    }

    /**
     * Repo: 批量获取配置的值
     *
     * 没有默认值的情况: $ids = ['id', 'id2']
     * 有默认值的情况: $ids = ['id' => 'default', 'id2' => 'default2']
     * 混合的情况 ['id', 'id2' => 'default2']
     *
     * @param array $ids
     * @return array
     */
    public function getValues(array $ids)
    {
        $values = [];
        foreach ($ids as $id => $default) {
            if (is_int($id)) {
                $id = $default;
            }
            $values[$id] = $this->getValue($id, $default);
        }

        return $values;
    }

    /**
     * Repo: 批量设置配置项
     *
     * @param array $values
     * @param string|array $allowedPrefixes
     * @return $this
     */
    public function setValues(array $values, $allowedPrefixes = null)
    {
        $isStartsWith = $this->isStartsWith;
        foreach ($values as $id => $value) {
            if ($allowedPrefixes && !$isStartsWith($id, $allowedPrefixes)) {
                continue;
            }
            $this->setValue($id, $value);
        }

        return $this;
    }

    /**
     * Repo: 生成可供前端表单加载是数据
     *
     * @param array $ids
     * @return array
     * @todo 待实现setting表单化后移除
     */
    public function getFormJson(array $ids)
    {
        $values = [];
        foreach ($this->getValues($ids) as $id => $value) {
            $id = strtolower(preg_replace('/[A-Z]/', '-$0', strtr($id, ['.' => '-', '_' => '-'])));
            $values['js-' . $id] = $value;
        }

        return json_encode($values, JSON_UNESCAPED_UNICODE);
    }
}
