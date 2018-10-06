<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Model\ReqQueryTrait;
use Miaoxing\User\Service\GroupModel;

/**
 * 用户分组
 */
class Group extends \Miaoxing\Plugin\BaseModel
{
    use ReqQueryTrait;

    protected $table = 'groups';

    protected $data = [
        'sort' => 50,
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
            'name' => '未分组',
        ]);
        array_unshift($this->data, $group);
        return $this;
    }

    public function getCustomerServiceGroups()
    {
        $this->customerServiceGroups ||
        $this->customerServiceGroups = wei()->group()->findAll(['isCustomerService' => self::CUSTOMER_SERVICE]);

        return $this->customerServiceGroups;
    }

    /**
     * Collection
     *
     * @param array $groups
     * @param int $deep
     * @return array
     */
    public function getTree($groups = [], $deep = 0)
    {
        $deep = (int) $deep;
        $deep--;

        /** @var $group Group */
        foreach ($this as $group) {
            $groups[] = $group;
            // 参数为0时,当前为-1,则一直获取下去
            if ($deep) {
                $groups = $group->getChildren()->findAll()->getTree($groups, $deep);
            }
        }

        return $groups;
    }

    public function getTreeToArray($groups = [])
    {
        /** @var $group Group */
        foreach ($this as $group) {
            $groups[] = $group->toArray();
            $groups = $group->getChildren()->desc('sort')->findAll()->getTreeToArray($groups);
        }

        return $groups;
    }

    /**
     * Record: 获取当前分组的子分组
     *
     * @return Group|Group[]
     */
    public function getChildren()
    {
        return wei()->group()->notDeleted()->andWhere(['parentId' => $this['id']])->desc('sort')->desc('id');
    }

    public function getNameWithPrefix()
    {
        if ($this['level'] == '0') {
            $prefix = '';
        } else {
            $prefix = '|' . str_repeat('--', $this['level']);
        }

        return $prefix . $this['name'];
    }

    /**
     * @return GroupModel|GroupModel[]
     * @throws \Exception
     */
    public function getGroupsFromCache()
    {
        return wei()->groupModel()
            ->desc('sort')
            ->cache(86400)
            ->tags(false)
            ->setCacheKey('groups:' . wei()->app->getId())
            ->indexBy('id')
            ->findAll();
    }
}
