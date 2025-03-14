<?php

namespace Miaoxing\Plugin\Command;

/**
 * @mixin \CacheMixin
 * @mixin \PluginMixin
 */
class PluginUnuse extends BaseCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    protected function handle()
    {
        $this->cache->delete('plugin:use');

        $this->suc('Remove default plugin');
    }

    protected function configure()
    {
        $this->setDescription('Remove the default plugin set by `plugin:use`');
    }
}
