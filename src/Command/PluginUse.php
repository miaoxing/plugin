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

        $this->plugin->getOneById($id);

        $this->cache->set('plugin:use', $id);

        $this->suc(sprintf('Set default plugin to "%s"', $id));
    }

    protected function configure()
    {
        $this->addArgument('plugin-id', InputArgument::REQUIRED, 'The id of plugin');
    }
}
