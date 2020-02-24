<?php

namespace Miaoxing\Plugin\Migration;

use Miaoxing\Services\Migration\BaseMigration;

class V20170406112858UpdateGroupsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        if (in_array('updateTime', $this->db->getTableFields('groups'))) {
            return;
        }

        $this->schema->table('groups')
            ->timestamp('updateTime')
            ->userstampsV1()
            ->softDeletableV1()
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->table('groups')
            ->dropColumn('updateTime')
            ->dropColumn('createUser')
            ->dropColumn('updateUser')
            ->dropColumn('deleteTime')
            ->dropColumn('deleteUser')
            ->exec();
    }
}
