<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \AppMixin
 * @mixin \PluginMixin
 */
class PluginList extends BaseCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->app->setId($this->getArgument('app'));

        $table = new Table($this->output);
        $table->setHeaders(['ID', 'Name', 'Version', 'Description', 'Installed']);

        foreach ($this->plugin->getAll() as $row) {
            $table->addRow($row->toArray() + [
                    'installed' => $this->plugin->isInstalled($row->getId()) ? 'Y' : 'N',
                ]);
        }
        $table->render();
    }

    protected function configure()
    {
        $this->setDescription('List the plugins')
            ->addArgument('app', InputArgument::OPTIONAL, 'The id of the app');
    }
}
