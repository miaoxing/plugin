<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \AppMixin
 * @mixin \PluginMixin
 */
class PluginUninstall extends BaseCommand
{
    public function handle()
    {
        $this->app->setId($this->getArgument('app'));
        $ret = $this->plugin->uninstall($this->getArgument('id'));
        return $this->ret($ret);
    }

    protected function configure()
    {
        $this->setDescription('Install the plugin')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the plugin')
            ->addArgument('app', InputArgument::OPTIONAL, 'The id of the app');
    }
}
