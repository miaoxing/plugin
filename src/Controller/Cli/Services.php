<?php

namespace Miaoxing\Plugin\Controller\Cli;

use Miaoxing\Plugin\BaseController;
use Miaoxing\Plugin\BasePlugin;
use Miaoxing\Plugin\CliDefinitionTrait;

class Services extends BaseController
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
            return $this->err('缺少服务名称');
        }

        $class = ucfirst($req['name']);
        $file = $this->getFile($plugin, $class);
        $this->createDir(dirname($file));

        $namespace = $this->getNamespace($plugin);

        $this->createFile($file, $namespace, $class);

        return $this->suc();
    }

    protected function getFile(BasePlugin $plugin, $name)
    {
        return $plugin->getBasePath() . '/src/Service/' . ucfirst($name) . '.php';
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

        return implode('\\', $parts) . '\Service';
    }

    protected function createFile($file, $namespace, $class)
    {
        $this->writeln('生成文件 ' . $this->cli->success($file));

        ob_start();
        require $this->plugin->getById('plugin')->getBasePath() .'/resources/stubs/service.php';
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
