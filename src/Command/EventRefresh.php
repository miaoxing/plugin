<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

class EventRefresh extends BaseCommand
{

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        wei()->app->setNamespace($this->getArgument('app'));
        wei()->plugin->getEvents(true);
        $this->suc('Refreshed the event cache!');
    }
    protected function configure()
    {
        $this->setDescription('Refreshed the event cache');
        $this->addArgument('app', InputArgument::OPTIONAL, 'Then name of the app', 'app');
    }
}
