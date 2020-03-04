<?php

namespace Miaoxing\Plugin\Migration;

use Miaoxing\Services\Migration\BaseMigration;

class V20170321181010AddStatusToAppsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('apps')
            ->tinyInt('status')->defaults(1)->after('industry')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->table('apps')
            ->dropColumn('status')
            ->exec();
    }
}
