<?php

namespace Miaoxing\Plugin\Controller\Cli;

use Miaoxing\Plugin\BaseController;
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
        $this->migration->migrate();

        return $this->suc();
    }

    public function rollbackAction($req)
    {
        $this->rollbackDefinition();

        $this->migration->rollback($req);

        return $this->suc();
    }

    public function createAction($req)
    {
        $this->createDefinition();

        return $this->migration->create($req);
    }

    public function statusAction()
    {
        $this->migration->status();

        return $this->suc();
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
