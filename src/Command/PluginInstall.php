<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

class PluginInstall extends BaseCommand
{
    protected function configure()
    {
        $this->setDescription('Install the plugin')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the plugin')
            ->addArgument('app', InputArgument::OPTIONAL, 'The name of the app', 'app');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        wei()->app->setNamespace($this->argument('app'));
        $ret = wei()->plugin->install($this->argument('id'));
        $this->ret($ret);
    }
}
