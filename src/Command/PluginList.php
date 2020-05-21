<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;

class PluginList extends BaseCommand
{

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        wei()->app->setNamespace($this->getArgument('app'));

        $table = new Table($this->output);
        $table->setHeaders(['ID', 'Name', 'Version', 'Description', 'Installed']);

        $plugin = wei()->plugin;
        $plugins = $plugin->getAll();
        foreach ($plugins as $row) {
            $table->addRow($row->toArray() + [
                    'installed' => $plugin->isInstalled($row->getId()) ? 'Y' : 'N',
                ]);
        }
        $table->render();
    }
    protected function configure()
    {
        $this->setDescription('List the plugins')
            ->addArgument('app', InputArgument::OPTIONAL, 'The name of the app', 'app');
    }
}
