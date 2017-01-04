<?php

namespace Miaoxing\Plugin;

use Miaoxing\Plugin\Service\Schema;
use Wei\Base;
use Wei\Db;

/**
 * @property Schema $schema
 * @property Db $db
 */
class BaseMigration extends Base
{
    /**
     * Run the migration.
     */
    public function up()
    {
    }

    /**
     * Revert the migration.
     */
    public function down()
    {
    }
}
