<?php

namespace Miaoxing\Plugin\Command;

/**
 * @mixin \ConfigMixin
 */
class ConfigPreload extends BaseCommand
{
    protected function configure()
    {
        $this->setDescription('Update the preload cache if it has expired');
    }

    protected function handle()
    {
        if ($this->config->updatePreloadIfExpired()) {
            $this->suc('Updated preload cache');
        } else {
            $this->suc('Preload cache is up to date');
        }
    }
}
