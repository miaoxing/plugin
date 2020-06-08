<?php

namespace Miaoxing\Plugin\Migration;

use Wei\Migration\BaseMigration;

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
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists($this->table);
    }
}
