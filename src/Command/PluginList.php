<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Services\Command\BaseCommand;
use Symfony\Component\Console\Helper\Table;

class PluginList extends BaseCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'plugin:list {app=app }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List the plugins';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        wei()->app->setNamespace($this->argument('app'));

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
}
