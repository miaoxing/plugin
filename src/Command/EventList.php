<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \AppMixin
 * @mixin \PluginMixin
 */
class EventList extends BaseCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->app->setId((int) $this->getArgument('app'));
        $events = $this->plugin->getEvents();

        $table = new Table($this->output);
        $table->setHeaders(['Name', 'Priority', 'Plugins']);

        foreach ($events as $name => $priorityToPlugins) {
            foreach ($priorityToPlugins as $priority => $plugins) {
                $table->addRow([
                    $name,
                    $priority,
                    implode(',', $plugins),
                ]);
            }
        }
        $table->render();
    }

    protected function configure()
    {
        $this->setDescription('List the available events');
        $this->addArgument('app', InputArgument::OPTIONAL, 'The id of the app');
    }
}
