<?php

namespace MiaoxingTest\Plugin\Model\Fixture;

trait DbTrait
{
    protected function createTable()
    {
        $db = $this->db;
        $db->query("CREATE TABLE p_test_user_groups (id INTEGER NOT NULL AUTO_INCREMENT, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))");
        $db->query("CREATE TABLE p_test_users (id INTEGER NOT NULL AUTO_INCREMENT, group_id INTEGER NOT NULL, name VARCHAR(50) NOT NULL, address VARCHAR(256) NOT NULL, PRIMARY KEY(id))");
    }

    protected function dropTable()
    {
        $db = $this->db;
        $db->query('DROP TABLE IF EXISTS p_test_user_groups');
        $db->query('DROP TABLE IF EXISTS p_test_users');
    }

    public function initFixtures()
    {
        $db = $this->db;

        $this->dropTable();
        $this->createTable();

        $db->insert('test_user_groups', array(
            'id' => '1',
            'name' => 'vip',
        ));

        $db->insert('test_users', array(
            'group_id' => '1',
            'name' => 'twin',
            'address' => 'test',
        ));

        $db->insert('test_users', array(
            'group_id' => '1',
            'name' => 'test',
            'address' => 'test',
        ));
    }
}
