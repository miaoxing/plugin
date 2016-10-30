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

    public function __construct(array $options)
    {
        parent::__construct($options);

        // TODO 避免后续输出导致session启动失败
        $this->session;
    }

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
