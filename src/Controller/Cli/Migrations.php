<?php

namespace Miaoxing\Plugin\Controller\Cli;

use miaoxing\plugin\BaseController;
use services\Migration;

/**
 * @property Migration $migration
 */
class Migrations extends BaseController
{
    public function indexAction()
    {
        return $this->migrateAction();
    }

    public function migrateAction()
    {
        return $this->migration->migrate();
    }

    public function rollbackAction()
    {
        return $this->migration->rollback();
    }
}
