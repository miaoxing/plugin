<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\BasePlugin;
use Symfony\Component\Console\Input\InputArgument;

class GController extends BaseCommand
{
    protected static $defaultName = 'g:controller';

    protected function configure()
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'The name of controller')
            ->addArgument('plugin-id', InputArgument::REQUIRED, 'The id of plugin');
    }

    /**
     * @return int
     * @throws \Exception
     */
    protected function handle()
    {
        $name = $this->input->getArgument('name');
        $id = $this->input->getArgument('plugin-id');

        $plugin = wei()->plugin->getOneById($id);

        $file = $this->getFile($plugin, $name);
        $this->createDir(dirname($file));

        list($class, $namespace) = $this->getNamespace($plugin, $name);

        $this->createFile($file, $namespace, $class);

        wei()->plugin->getConfig(true);

        return $this->suc('创建成功');
    }

    protected function getFile(BasePlugin $plugin, $name)
    {
        $parts = explode('/', $name);

        foreach ($parts as $i => $part) {
            $parts[$i] = ucfirst($this->str->camel($part));
        }
        $name = ucfirst(implode('/', $parts));

        return $plugin->getBasePath() . '/src/Controller/' . $name . 'Controller.php';
    }

    protected function createDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
    }

    protected function getNamespace(BasePlugin $plugin, $name)
    {
        $parts = explode('/', $name);
        foreach ($parts as $i => $part) {
            $parts[$i] = ucfirst($this->str->camel($part));
        }

        $name = ucfirst(array_pop($parts)) . 'Controller';
        $namespace = $this->getPluginNamespace($plugin) . '\Controller';
        if ($parts) {
            $namespace .= '\\' . implode('\\', $parts);
        }

        return [$name, $namespace];
    }

    protected function getPluginNamespace($plugin)
    {
        $class = get_class($plugin);
        $parts = explode('\\', $class);
        array_pop($parts);

        return implode('\\', $parts);
    }

    protected function createFile($file, $namespace, $class)
    {
        $this->suc('生成文件 ' . $file);

        ob_start();
        require $this->plugin->getById('plugin')->getBasePath() . '/resources/stubs/controller.php';
        $content = ob_get_clean();

        file_put_contents($file, $content);
        chmod($file, 0777);
    }
}
