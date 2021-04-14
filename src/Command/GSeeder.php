<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\Service\Seeder;
use ReflectionClass;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \PluginMixin
 */
class GSeeder extends BaseCommand
{
    protected function configure()
    {
        $this->setDescription('Generate a plugin seeder class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the seeder')
            ->addArgument('plugin', InputArgument::OPTIONAL, 'The name of the plugin');
    }

    /**
     * @return int|void
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function handle()
    {
        $plugin = $this->plugin->getOneById($this->getArgument('plugin'));
        $path = $plugin->getBasePath() . '/src/Seeder';
        $reflection = new ReflectionClass($plugin);
        $namespace = $reflection->getNamespaceName() . '\\Seeder';

        Seeder::setOutput($this->output)->create([
            'name' => $this->getArgument('name'),
            'path' => $path,
            'namespace' => $namespace,
        ]);
    }
}
