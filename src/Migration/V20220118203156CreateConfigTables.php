<?php

namespace Miaoxing\Plugin\Migration;

use Wei\Migration\BaseMigration;

class V20220118203156CreateConfigTables extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('configs')
            ->id()
            ->uBigInt('app_id')->comment('应用编号')
            ->string('name', 64)
            ->char('type', 1)->comment('值的类型,s:字符串')
            ->string('value', 2048)
            ->string('comment', 32)
            ->timestamps()
            ->userstamps()
            ->softDeletable()
            ->exec();

        $this->schema->table('global_configs')
            ->id()
            ->string('name', 64)
            ->char('type', 1)->comment('值的类型,s:字符串')
            ->string('value', 2048)
            ->bool('preload')->comment('是否生成到配置文件中')
            ->string('comment', 32)
            ->timestamps()
            ->userstamps()
            ->softDeletable()
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists(['configs', 'global_configs']);
    }
}
