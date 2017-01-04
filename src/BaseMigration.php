<?php

namespace Miaoxing\Plugin;

use Wei\Schema;
use Wei\Base;
use Wei\Db;

/**
 * @property \Wei\Schema $schema
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
