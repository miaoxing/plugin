<?php

namespace Miaoxing\Plugin\Command;

use ReflectionClass;
use Symfony\Component\Console\Input\InputArgument;
use Wei\Migration;

class GMigration extends BaseCommand
{
    protected function configure()
    {
        $this->setDescription('Generate a plugin migration class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the migration')
            ->addArgument('plugin', InputArgument::OPTIONAL, 'The name of the plugin');
    }

    /**
     * @return int|void
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function handle()
    {
        $plugin = wei()->plugin->getOneById($this->getArgument('plugin'));
        $path = $plugin->getBasePath() . '/src/Migration';
        $reflection = new ReflectionClass($plugin);
        $namespace = $reflection->getNamespaceName() . '\\Migration';

        Migration::setOutput($this->output)->create([
            'name' => $this->getArgument('name'),
            'path' => $path,
            'namespace' => $namespace,
        ]);
    }
}
