<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \AppMixin
 * @mixin \PluginMixin
 */
class PluginInstall extends BaseCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->app->setId($this->getArgument('app'));
        $ret = $this->plugin->install($this->getArgument('id'));
        $this->ret($ret);
    }

    protected function configure()
    {
        $this->setDescription('Install the plugin')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the plugin')
            ->addArgument('app', InputArgument::OPTIONAL, 'The id of the app');
    }
}
