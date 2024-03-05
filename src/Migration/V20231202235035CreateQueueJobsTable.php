<?php

namespace Miaoxing\Plugin\Migration;

use Wei\Migration\BaseMigration;

class V20231202235035CreateQueueJobsTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('queue_jobs')
            ->bigId()
            ->string('queue')->index('queue')
            ->longText('payload')
            ->uTinyInt('attempts')
            ->timestamp('reserved_at')
            ->timestamp('available_at')
            ->timestamp('created_at')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('queue_jobs');
    }
}
