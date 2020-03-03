<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Services\Command\BaseCommand;

class PluginRefresh extends BaseCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'plugin:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the plugin cache';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        wei()->plugin->getConfig(true);
        $this->info('Refreshed the plugin config!');
    }
}
