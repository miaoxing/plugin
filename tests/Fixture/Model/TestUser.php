<?php

namespace MiaoxingTest\Plugin\Fixture\Model;

use Miaoxing\Plugin\Service\Model;

/**
 * @property string id
 * @property string name
 * @property int groupId
 * @property string address
 */
class TestUser extends Model
{
    protected $table = 'users';

    protected $scopes;

    protected $loadTimes;

    protected $eventResult;

    protected $casts = [
        'id' => 'int',
        'group_id' => 'int',
    ];

    protected $data = array(
        'group_id' => 0,
    );

    public function getPost()
    {
        return $this->db->find('post', array('member_id' => $this->data['id']));
    }

    public function getGroup()
    {
        return $this->db->find('member_group', array('id' => $this->data['group_id']));
    }

    public function getPosts()
    {
        $this->posts = $this->db->findAll('post', array('member_id' => $this->data['id']));
        return $this->posts;
    }

    public function afterLoad()
    {
        $this->loadTimes++;
    }

    public function getLoadTimes()
    {
        return $this->loadTimes;
    }

    public function beforeCreate()
    {
        $this->eventResult .= 'beforeCreate->';
    }

    public function afterCreate()
    {
        $this->eventResult .= 'afterCreate->';
    }

    public function beforeSave()
    {
        $this->eventResult .= 'beforeSave->';
    }

    public function afterSave()
    {
        $this->eventResult .= 'afterSave';
    }

    public function beforeDestroy()
    {
        $this->eventResult .= 'beforeDestroy->';
    }

    public function afterDestroy()
    {
        $this->eventResult .= 'afterDestroy';
    }

    public function getEventResult()
    {
        return $this->eventResult;
    }

    public function getAddressAttribute()
    {
        return $this->data['address'] ?: 'default address';
    }
}
