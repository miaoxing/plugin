<?php

namespace Miaoxing\Plugin\Migration;

use Miaoxing\Plugin\Service\PresetColumns;
use Wei\Migration\BaseMigration;

class V20161030000000CreateUsersTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('users')->tableComment('用户')
            ->bigId()
            ->add(PresetColumns::appId())
            ->string('out_id', 32)
            ->bool('is_admin')
            ->string('nick_name', 32)
            ->string('remark_name', 32)
            ->string('username', 64)
            ->string('name', 16)
            ->string('email')
            ->string('mobile', 16)
            ->timestamp('mobile_verified_at')->comment('手机校验时间')
            ->string('phone', 16)
            ->string('password')
            ->uTinyInt('sex')->defaults(1)
            ->string('country', 32)
            ->string('province', 32)
            ->string('city', 32)
            ->string('district', 32)
            ->string('address', 128)
            ->string('signature', 64)
            ->add(PresetColumns::isEnabled())
            ->string('avatar')
            ->timestamp('last_login_at')
            ->timestamps()
            ->userstamps()
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('users');
    }
}
