<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Services\Command\BaseCommand;

class EventRefresh extends BaseCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'event:refresh {app=app : The name of the app}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the event cache';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        wei()->app->setNamespace($this->argument('app'));
        wei()->plugin->getEvents(true);
        $this->info('Refreshed the event cache!');
    }
}
