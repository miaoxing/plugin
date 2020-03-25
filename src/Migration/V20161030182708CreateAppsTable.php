<?php

namespace Miaoxing\Plugin\Migration;

use Miaoxing\Services\Migration\BaseMigration;

class V20161030182708CreateAppsTable extends BaseMigration
{
    protected $table = 'apps';

    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table($this->table)
            ->id()
            ->int('user_id')
            ->text('plugin_ids')
            ->string('name', 16)
            ->string('title', 32)
            ->char('secret', 32)
            ->string('domain', 128)
            ->string('description')
            ->string('industry', 16)
            ->tinyInt('status')->defaults(1)
            ->string('configs')->defaults('[]')
            ->timestamps()
            ->userstamps()
            ->exec();

        $now = date('Y-m-d H:i:s');
        $this->db->batchInsert($this->table, [
            [
                'user_id' => 1,
                'name' => 'app',
                'title' => 'app',
                'plugin_ids' => '',
                'created_at' => $now,
                'created_by' => 1,
                'updated_at' => $now,
                'updated_by' => 1,
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists($this->table);
    }
}
