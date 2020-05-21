<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

class EventList extends BaseCommand
{
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

    protected function configure()
    {
        $this->setDescription('List the available events');
        $this->addArgument('app', InputArgument::OPTIONAL, 'Then name of the app', 'app');
    }
}
