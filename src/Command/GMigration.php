<?php

namespace Miaoxing\Plugin\Command;

use Symfony\Component\Console\Input\InputArgument;
use Wei\Migration;

/**
 * @mixin \PluginMixin
 */
class GMigration extends BaseCommand
{
    use PluginIdTrait;

    protected function configure()
    {
        $this->setAliases(['migration:g'])
            ->setDescription('Generate a plugin migration class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration')
            ->addArgument('plugin-id', InputArgument::OPTIONAL, 'The id of plugin');
    }

    /**
     * @return int|void
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function handle()
    {
        $plugin = $this->plugin->getOneById($this->getPluginId());
        $path = $plugin->getBasePath() . '/src/Migration';
        $reflection = new \ReflectionClass($plugin);
        $namespace = $reflection->getNamespaceName() . '\Migration';

        Migration::setOutput($this->output)->create([
            'name' => $this->getArgument('name'),
            'path' => $path,
            'namespace' => $namespace,
        ]);
    }
}
