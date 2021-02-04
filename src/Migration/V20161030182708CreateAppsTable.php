<?php

namespace Miaoxing\Plugin\Migration;

use Wei\Migration\BaseMigration;

class V20161030182708CreateAppsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('apps')->tableComment('应用')
            ->id()
            ->uInt('user_id')->comment('所属用户的编号')
            ->string('plugin_ids', 2048)->comment('已安装的插件编号')
            ->string('name', 64)->comment('名称')
            ->string('domain', 128)->comment('绑定的域名')
            ->string('description')->comment('描述')
            ->uTinyInt('status')->comment('状态')->defaults(1)
            ->timestamps()
            ->userstamps()
            ->index('domain')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('apps');
    }
}
