<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

class PluginUninstall extends BaseCommand
{
    public function handle()
    {
        wei()->app->setNamespace($this->getArgument('app'));
        $ret = wei()->plugin->uninstall($this->getArgument('id'));
        return $this->ret($ret);
    }

    protected function configure()
    {
        $this->setDescription('Install the plugin')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the plugin')
            ->addArgument('app', InputArgument::OPTIONAL, 'The name of the app', 'app');
    }
}
