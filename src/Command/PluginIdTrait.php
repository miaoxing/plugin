<?php

namespace Miaoxing\Plugin\Command;

/**
 * @mixin \CacheMixin
 */
trait PluginIdTrait
{
    protected function getPluginId(): string
    {
        $pluginId = $this->input->getArgument('plugin-id') ?: $this->cache->get('plugin:use');
        if ($pluginId) {
            return $pluginId;
        }

        throw new \RuntimeException('Not enough arguments (missing: "plugin-id").');
    }
}
