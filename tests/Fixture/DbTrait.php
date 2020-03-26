<?php

namespace MiaoxingTest\Plugin\Fixture;

trait DbTrait
{
    protected function createTable()
    {
        $db = $this->db;
        $db->query("CREATE TABLE pre_user_groups (id INTEGER NOT NULL AUTO_INCREMENT, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))");
        $db->query("CREATE TABLE pre_users (id INTEGER NOT NULL AUTO_INCREMENT, group_id INTEGER NOT NULL, name VARCHAR(50) NOT NULL, address VARCHAR(256) NOT NULL, PRIMARY KEY(id))");
        $db->query("CREATE TABLE pre_posts (id INTEGER NOT NULL AUTO_INCREMENT, user_id INTEGER NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))");
        $db->query("CREATE TABLE pre_tags (id INTEGER NOT NULL AUTO_INCREMENT, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))");
        $db->query("CREATE TABLE pre_post_tags (post_id INTEGER NOT NULL, tag_id INTEGER NOT NULL)");
    }

    protected function dropTable()
    {
        $db = $this->db;
        $db->query('DROP TABLE IF EXISTS pre_user_groups');
        $db->query('DROP TABLE IF EXISTS pre_users');
        $db->query('DROP TABLE IF EXISTS pre_posts');
        $db->query('DROP TABLE IF EXISTS pre_tags');
        $db->query('DROP TABLE IF EXISTS pre_post_tags');
    }

    public function initFixtures()
    {
        $db = $this->db;

        $db->setOption('tablePrefix', 'pre_');

        $this->dropTable();
        $this->createTable();

        $db->insert('user_groups', array(
            'id' => '1',
            'name' => 'vip',
        ));

        $db->insert('users', array(
            'group_id' => '1',
            'name' => 'twin',
            'address' => 'test',
        ));

        $db->insert('users', array(
            'group_id' => '1',
            'name' => 'test',
            'address' => 'test',
        ));

        $db->insert('posts', array(
            'user_id' => '1',
            'name' => 'my first post',
        ));

        $db->insert('posts', array(
            'user_id' => '1',
            'name' => 'my second post',
        ));

        $db->insert('tags', array(
            'id' => '1',
            'name' => 'database',
        ));

        $db->insert('tags', array(
            'id' => '2',
            'name' => 'PHP',
        ));

        $db->insert('post_tags', array(
            'post_id' => '1',
            'tag_id' => '1',
        ));

        $db->insert('post_tags', array(
            'post_id' => '1',
            'tag_id' => '2',
        ));

        $db->insert('post_tags', array(
            'post_id' => '2',
            'tag_id' => '1',
        ));
    }
}
