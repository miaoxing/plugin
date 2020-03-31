<?php

namespace Miaoxing\Plugin\Migration;

use Miaoxing\Services\Migration\BaseMigration;
use Miaoxing\Services\Service\Time;

class V20161030000000CreateUsersTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $table = $this->schema->table('users');
        $table->tableComment('用户')
            ->id()
            ->int('app_id')
            ->string('out_id', 32)
            ->char('wechat_open_id', 28)->comment('微信的OpenID')
            ->char('wechat_union_id', 29)
            ->bool('admin')
            ->string('nick_name', 32)
            ->string('remark_name', 32)
            ->string('username', 64)
            ->string('name', 16)
            ->string('email', 256)
            ->string('mobile', 16)
            ->timestamp('mobile_verified_at')->comment('手机校验时间')
            ->string('phone', 16)
            ->string('salt', 32)
            ->string('password', 128)
            ->tinyInt('sex')
            ->string('country', 32)
            ->string('province', 32)
            ->string('city', 32)
            ->string('area', 32)
            ->string('address', 128)
            ->string('signature', 64);

        $table->string('avatar', 256)
            ->int('group_id')->comment('用户组')
            ->decimal('money', 16, 2)->comment('账户余额')
            ->decimal('recharge_money', 16, 2)->comment('充值账户余额')
            ->int('score')->comment('积分')
            ->timestamp('last_login_at')
            ->timestamp('unsubscribed_at')->comment('取关时间')
            ->bool('is_subscribed')
            ->bool('enable')->defaults(1)->comment('是否启用')
            ->string('source', 16)->comment('用户来源')
            ->timestamps()
            ->userstamps();

        $table->index('wechat_open_id')
            ->index('is_subscribed')
            ->exec();

        $salt = wei()->password->generateSalt();
        $this->db->insert('users', [
            'username' => 'admin',
            'admin' => 1,
            'salt' => $salt,
            'password' => wei()->password->hash('password', $salt),
            'created_at' => Time::now(),
            'updated_at' => Time::now(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('users');
    }
}
