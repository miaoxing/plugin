<?php

namespace Miaoxing\Plugin\Migration;

use Miaoxing\Plugin\Service\UserModel;
use Wei\Migration\BaseMigration;
use Wei\QueryBuilder;

class V20230201181853AddAdminTypeToUsersTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('users')
            ->uTinyInt('admin_type')->comment('管理员类型')->defaults(UserModel::ADMIN_TYPE_DEFAULT)->after('out_id')
            ->exec();

        // NOTE: 不使用 UserModel，兼容单元测试时没有 appId 导致错误
        // UserModel::asc('id')->findOrInitBy()->save(['adminType' => UserModel::ADMIN_TYPE_SUPER]);
        $user = QueryBuilder::table('users')->asc('id')->first();
        if ($user) {
            QueryBuilder::table('users')->where('id', $user['id'])->update('admin_type', UserModel::ADMIN_TYPE_SUPER);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->table('users')
            ->dropColumn('admin_type')
            ->exec();
    }
}
