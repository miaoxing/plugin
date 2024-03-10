<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\BasePlugin;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \PluginPropMixin
 */
class GJob extends BaseCommand
{
    use PluginIdTrait;

    protected function configure()
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of job')
            ->addArgument('plugin-id', InputArgument::OPTIONAL, 'The id of plugin');
    }

    /**
     * @return int
     * @throws \Exception
     */
    protected function handle()
    {
        $name = $this->input->getArgument('name');
        $id = $this->getPluginId();

        $plugin = $this->plugin->getOneById($id);

        $class = ucfirst($name);
        $file = $this->getFile($plugin, $class);
        $this->createDir(dirname($file));

        $namespace = $this->getNamespace($plugin);

        $this->createFile($file, $namespace, $class);

        return $this->suc('Generated successfully');
    }

    protected function getFile(BasePlugin $plugin, $name)
    {
        return $plugin->getBasePath() . '/src/Job/' . ucfirst($name) . '.php';
    }

    protected function createDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
    }

    protected function getNamespace(BasePlugin $plugin)
    {
        $class = get_class($plugin);
        $parts = explode('\\', $class);
        array_pop($parts);

        return implode('\\', $parts) . '\Job';
    }

    protected function createFile($file, $namespace, $class)
    {
        $this->suc('Generate file ' . $file);

        ob_start();
        require $this->plugin->getById('plugin')->getBasePath() . '/stubs/job.php';
        $content = ob_get_clean();

        file_put_contents($file, $content);
        chmod($file, 0777);
    }
}
