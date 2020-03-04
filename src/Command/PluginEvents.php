<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Services\Command\BaseCommand;

class PluginEvents extends BaseCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'plugin:events {app=app : The name of the app}';

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
        $events = wei()->plugin->getEvents();
        dump($events);
    }
}
