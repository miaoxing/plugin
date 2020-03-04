<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Services\Command\BaseCommand;

class PluginUninstall extends BaseCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'plugin:uninstall {id : The id of the plugin}
        {app=app : The name of the app}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the plugin';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        wei()->app->setNamespace($this->argument('app'));
        $ret = wei()->plugin->uninstall($this->argument('id'));
        $this->writeRet($ret);
    }
}
