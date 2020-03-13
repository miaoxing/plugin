<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

class EventList extends BaseCommand
{
    protected function configure()
    {
        $this->setDescription('List the available events');
        $this->addArgument('app', InputArgument::OPTIONAL, 'Then name of the app', 'app');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        wei()->app->setNamespace($this->getArgument('app'));
        $events = wei()->plugin->getEvents();
        dump($events);
    }
}
