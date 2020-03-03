<?php

namespace Miaoxing\Plugin\Migration;

use Miaoxing\Services\Migration\BaseMigration;

class V20161030000000CreateUsersTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $table = $this->schema->table('user');
        $table->tableComment('用户')
            ->id()
            ->int('appId')
            ->string('outId', 32)
            ->char('wechatOpenId', 28)
            ->char('wechatUnionId', 29)->comment('微信的OpenID')
            ->bool('admin')
            ->string('nickName', 32)
            ->string('remarkName', 32)
            ->string('username', 64)
            ->string('name', 16)
            ->string('email', 256)
            ->string('mobile', 16)
            ->string('phone', 16)
            ->string('salt', 32)
            ->string('password', 128)
            ->tinyInt('gender')
            ->string('country', 32)
            ->string('province', 32)
            ->string('city', 32)
            ->string('area', 32)
            ->string('address', 128)
            ->string('signature', 64);

        $table->string('headImg', 256)
            ->int('groupId')->comment('用户组')
            ->text('department')->comment('部门')
            ->string('position', 32)->comment('职位')
            ->text('extAttr')->comment('额外参数')
            ->decimal('money', 16, 2)->comment('账户余额')
            ->decimal('rechargeMoney', 16, 2)->comment('充值账户余额')
            ->int('score')->comment('积分')
            ->timestamp('lastLoginTime')
            ->timestamp('unsubscribeTime')->comment('取关时间')
            ->bool('isValid')
            ->bool('enable')->comment('是否启用')
            ->string('source', 16)->comment('用户来源')
            ->timestamp('lastPaidTime')
            ->timestamp('mobileVerifiedAt')->comment('手机校验时间')
            ->timestampsV1()
            ->userstampsV1();

        $table->index('wechatOpenId')
            ->index('isValid')
            ->exec();

        $this->db->insert('user', [
            'username' => 'admin',
            'admin' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('groups');
    }
}
