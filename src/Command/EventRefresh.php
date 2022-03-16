<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \AppMixin
 * @mixin \PluginMixin
 */
class EventRefresh extends BaseCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->app->setId($this->getArgument('app'));
        $this->plugin->getEvents(true);
        $this->suc('Refreshed the event cache!');
    }

    protected function configure()
    {
        $this->setDescription('Refreshed the event cache');
        $this->addArgument('app', InputArgument::OPTIONAL, 'The id of the app');
    }
}
