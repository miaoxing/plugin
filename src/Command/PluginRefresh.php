<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\Service\Plugin;

class PluginRefresh extends BaseCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Plugin::loadConfig(true);
        $this->suc('Refreshed the plugin config!');
    }

    protected function configure()
    {
        $this->setDescription('Refresh the plugin cache');
    }
}
