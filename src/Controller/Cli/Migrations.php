<?php

namespace Miaoxing\Plugin\Controller\Cli;

use miaoxing\plugin\BaseController;
use Miaoxing\Plugin\CliDefinition;
use services\Migration;

/**
 * @property Migration $migration
 */
class Migrations extends BaseController
{
    use CliDefinition;

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

    public function makeAction($req)
    {
        $this->makeDefinition();

        return $this->migration->make($req);
    }

    protected function makeDefinition()
    {
        $this->addArgument('name');
        $this->addOption('path');
    }
}
