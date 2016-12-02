<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * Scheme
 */
class SchemeTest extends BaseTestCase
{
    /**
     * 生成SQL语句
     */
    public function testGetSql()
    {
        $sql = wei()->scheme->table('table')
            ->id()
            ->int('user_id')
            ->string('name')
            ->text('description')
            ->timestampsV2()
            ->userstampsV2()
            ->getSql();

        $this->assertEquals('CREATE TABLE table (
  id int unsigned NOT NULL AUTO_INCREMENT,
  user_id int unsigned NOT NULL DEFAULT 0,
  name varchar(255) NOT NULL DEFAULT \'\',
  description text NOT NULL,
  created_at timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  updated_at timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  created_by int unsigned NOT NULL DEFAULT 0,
  updated_by int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY id (id)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci', $sql);
    }
}
