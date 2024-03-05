<?php

namespace Miaoxing\Plugin\Migration;

use Wei\Migration\BaseMigration;

class V20231202235107CreateQueueFailedJobsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('queue_failed_jobs')
            ->bigId()
            ->text('connection')
            ->text('queue')
            ->longText('payload')
            ->longText('exception')
            ->timestamp('failed_at')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('queue_failed_jobs');
    }
}
