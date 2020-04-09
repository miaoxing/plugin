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
            ->bool('admin')
            ->string('nick_name', 32)
            ->string('remark_name', 32)
            ->string('username', 64)
            ->string('name', 16)
            ->string('email')
            ->string('mobile', 16)
            ->timestamp('mobile_verified_at')->comment('手机校验时间')
            ->string('phone', 16)
            ->string('salt', 32)
            ->string('password', 128)
            ->tinyInt('sex')->defaults(1)
            ->string('country', 32)
            ->string('province', 32)
            ->string('city', 32)
            ->string('area', 32)
            ->string('address', 128)
            ->string('signature', 64);

        $table->bool('enable')->defaults(1)->comment('是否启用')
            ->string('avatar')
            ->timestamp('last_login_at')
            ->timestamps()
            ->userstamps()
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
