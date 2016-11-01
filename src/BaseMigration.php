<?php

namespace Miaoxing\Plugin;

use services\Scheme;
use Wei\Base;
use Wei\Db;

/**
 * @property Scheme $scheme
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
