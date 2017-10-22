<?php

namespace Miaoxing\Plugin\Service;

/**
 * 用户分组
 */
class Group extends \miaoxing\plugin\BaseModel
{
    protected $table = 'groups';

    protected $data = [
        'sort' => 50
    ];

    protected $customerServiceGroups;

    /**
     * 是否是客服小组
     */
    const CUSTOMER_SERVICE = 1;

    public function isReserved()
    {
        return in_array($this['id'], [0, 1, 2]);
    }

    public function unshift(Group $group)
    {
        array_unshift($this->data, $group);
        return $this;
    }

    /**
     * Coll: 附加未分组数据
     */
    public function withUngroup()
    {
        $group = wei()->group()->setData([
            'id' => 0,
            'name' => '未分组'
        ]);
        array_unshift($this->data, $group);
        return $this;
    }

    public function getCustomerServiceGroups()
    {
        $this->customerServiceGroups ||
        $this->customerServiceGroups = wei()->group()->findAll(['isCustomerService' => Group::CUSTOMER_SERVICE]);

        return $this->customerServiceGroups;
    }
}
