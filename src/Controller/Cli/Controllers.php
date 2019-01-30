<?php

namespace Miaoxing\Plugin\Controller\Cli;

use Miaoxing\Plugin\BaseController;
use Miaoxing\Plugin\BasePlugin;
use Miaoxing\Plugin\CliDefinitionTrait;
use Miaoxing\Plugin\Service\Str;

/**
 * @property Str $str
 */
class Controllers extends BaseController
{
    use CliDefinitionTrait;

    public function createAction($req)
    {
        $this->createDefinition();

        if (!$req['plugin']) {
            return $this->err('缺少插件编号');
        }

        $plugin = $this->plugin->getById($req['plugin']);
        if (!$plugin) {
            return $this->err(sprintf('插件"%s"不存在', $req['plugin']));
        }

        if (!$req['name']) {
            return $this->err('缺少控制器名称');
        }

        $file = $this->getFile($plugin, $req['name']);
        $this->createDir(dirname($file));

        list($class, $namespace) = $this->getNamespace($plugin, $req['name']);

        $this->createFile($file, $namespace, $class);

        $this->plugin->getConfig(true);

        return $this->suc();
    }

    protected function getFile(BasePlugin $plugin, $name)
    {
        $parts = explode('/', $name);

        foreach ($parts as $i => $part) {
            $parts[$i] = ucfirst($this->str->camel($part));
        }
        $name = ucfirst(implode('/', $parts));

        return $plugin->getBasePath() . '/src/Controller/' . $name . '.php';
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

        $name = ucfirst(array_pop($parts));
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
        $this->writeln('生成文件 ' . $this->cli->success($file));

        ob_start();
        require $this->plugin->getById('plugin')->getBasePath() . '/resources/stubs/controller.php';
        $content = ob_get_clean();

        file_put_contents($file, $content);
        chmod($file, 0777);
    }

    protected function createDefinition()
    {
        $this->addArgument('plugin');
        $this->addArgument('name');
    }

    /**
     * @param string $message
     */
    protected function writeln($message)
    {
        fwrite(STDERR, $message . "\n");
    }
}
