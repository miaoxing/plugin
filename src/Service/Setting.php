<?php

namespace Miaoxing\Plugin\Service;

/**
 * @property \Wei\Cache $cache
 * @property \Wei\Validator\StartsWith $isStartsWith
 */
class Setting extends \miaoxing\plugin\BaseModel
{
    protected $table = 'setting';

    protected $labels = [
        'site' => '官网设置',
        'site.logo' => '站点LOGO',
        'site.title' => '站点标题',
        'site.announcements' => '站点公告',

        'mall' => '微商城设置',
        'mall.cartTimeout' => '购物车超时时间',

        'reserve' => '预约设置',
        'reserve.info' => '预约说明',
        'reserve.phone' => '预约电话',
        'reserve.reserveAddress' => '预约地址',

        'phone' => '电话设置',
        'phone.sellPhone' => '销售电话',
        'phone.servicePhone' => '服务电话',

        'orders' => '订单设置',
        'orders.confirmText' => '确认支付的提示',
        'orders.enableWechatAddress' => '是否启用"从微信选择地址"功能',

        'orderMailer' => '订单邮件',
        'orderMailer.newOrderEmail' => '下单提醒的邮件接收地址',

        'refundMailer' => '退款邮件',
        'refundMailer.newRefundEmail' => '退款提醒的邮件接收地址',

        'questionMailer' => '提问邮件',
        'questionMailer.newQuestionEmail' => '提问提醒的邮件接收地址',

        'wechatQrcodes' => '微信二维码',
        'wechatQrcodes.description' => '用户二维码说明',

        'products' => '商品设置',
        'products.categoryLevel' => '商品分类层级',
        'products.listLayout' => '列表默认布局',
        //'products.tagQueryType' => '商品标签的查询类型',

        'dealer' => '经销商设置',
        'dealer.rule' => '佣金规则',

        'shakeRace' => '摇一摇竞赛',
        'shakeRace.qr_code' => '摇一摇竞赛二维码',
        //'wallet' => '账户配置',
        //'wallet.recharge' => '充值配置' // 不在站点设置显示
    ];

    protected $editorFields = [
        'wechatQrcodes.description',
        'dealer.rule',
    ];

    /**
     * {@inheritdoc}
     */
    protected $providers = [
        'cache' => 'nearCache',
    ];

    /**
     * @param string $id
     * @param mixed $default
     * @return $this|$this[]
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
