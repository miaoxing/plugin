<?php

namespace Miaoxing\Plugin\Migration;

use Miaoxing\Plugin\BaseMigration;

class V20161112150216CreateSettingTable extends BaseMigration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->schema->table('setting')
            ->string('id', 128)
            ->mediumText('value')
            ->primary('id')
            ->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->schema->dropIfExists('setting');
    }
}
