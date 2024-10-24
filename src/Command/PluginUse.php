<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \CacheMixin
 * @mixin \PluginMixin
 */
class PluginUse extends BaseCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    protected function handle()
    {
        $id = $this->input->getArgument('plugin-id');
        if (!$id) {
            $use = $this->cache->get('plugin:use');
            if (!$use) {
                $this->suc('Default Plugin not set');
                return;
            }
            $this->suc(sprintf('The default plugin is: %s', $use));
            return;
        }

        $this->plugin->getOneById($id);

        $this->cache->set('plugin:use', $id);

        $this->suc(sprintf('Set default plugin to "%s"', $id));
    }

    protected function configure()
    {
        $this->setDescription('Set the default plugin, which will be used when run the commands without plugin id')
            ->addArgument('plugin-id', InputArgument::OPTIONAL, 'The id of plugin');
    }
}
