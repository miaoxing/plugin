<?php

namespace Miaoxing\Plugin\Command;

class PluginRefresh extends BaseCommand
{
    protected function configure()
    {
        $this->setDescription('Refresh the plugin cache');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        wei()->plugin->getConfig(true);
        $this->suc('Refreshed the plugin config!');
    }
}
