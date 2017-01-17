<?php

namespace Miaoxing\Plugin\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20170117162835CreateGroupsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('groups')->tableComment('用户分组')
            ->id()
            ->int('wechatId')->comment('微信中的分组ID')
            ->string('name', 64)
            ->int('wechatCount')->comment('微信分组用户数')
            ->int('sort')->defaults(50)
            ->tinyInt('status')
            ->bool('isCustomerService')
            ->timestamp('createTime')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('groups');
    }
}
