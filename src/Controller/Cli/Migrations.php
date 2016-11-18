<?php

namespace Miaoxing\Plugin\Controller\Cli;

use miaoxing\plugin\BaseController;
use Miaoxing\Plugin\CliDefinition;
use Miaoxing\Plugin\Service\Migration;

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

    public function rollbackAction($req)
    {
        $this->rollbackDefinition();

        return $this->migration->rollback($req);
    }

    public function createAction($req)
    {
        $this->createDefinition();

        return $this->migration->create($req);
    }

    public function statusAction()
    {
        return $this->migration->status();
    }

    protected function rollbackDefinition()
    {
        $this->addOption('target', 't');
    }

    protected function createDefinition()
    {
        $this->addArgument('name');
        $this->addOption('path', 'p');
        $this->addOption('plugin-id', 'i');
    }
}
