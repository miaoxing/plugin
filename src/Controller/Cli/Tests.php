<?php

namespace Miaoxing\Plugin\Controller\Cli;

use miaoxing\plugin\BaseController;
use miaoxing\plugin\BasePlugin;
use Miaoxing\Plugin\CliDefinition;
use Miaoxing\Plugin\Service\Cli;

/**
 * @property Cli $cli
 */
class Tests extends BaseController
{
    use CliDefinition;

    public function createAction($req)
    {
        $cli = $this->cli;
        $this->createDefinition();

        if (!$req['plugin']) {
            return $this->err('缺少插件编号');
        }

        $plugin = $this->plugin->getById($req['plugin']);
        if (!$plugin) {
            return $this->err(sprintf('插件"%s"不存在', $req['plugin']));
        }

        $classes = $plugin->getControllerMap();
        foreach ($classes as $class) {
            $testFile = $this->getTestFile($plugin, $class);
            if (is_file($testFile)) {
                $this->writeln('文件已存在 ' . $cli->success($testFile));
                continue;
            }

            $this->createDir(dirname($testFile));

            list($namespace, $class) = $this->getTestClass($class);

            $this->createFile($testFile, $namespace, $class);
        }

        return $this->suc();
    }

    protected function getTestFile(BasePlugin $plugin, $class)
    {
        // Remove vendorName/packageName
        $parts = explode('\\', $class);
        array_shift($parts);
        array_shift($parts);

        $file = $plugin->getBasePath() . '/tests/' . implode('/', $parts) . 'Test.php';

        return $file;
    }

    /**
     * 类名如 Miaoxing\Xxx\Controller\Xxx
     * 转换为 [MiaoxingTest\Xxx\Controller, XxxTest]
     *
     * @param string $class
     * @return array
     */
    protected function getTestClass($class)
    {
        $parts = explode('\\', $class);
        $parts[0] .= 'Test';

        $count = count($parts) - 1;
        $class = $parts[$count] . 'Test';

        unset($parts[$count]);
        $namespace = implode('\\', $parts);

        return [$namespace, $class];
    }

    protected function createDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
    }

    protected function createFile($file, $namespace, $class)
    {
        $this->writeln('生成文件 ' . $this->cli->success($file));

        ob_start();
        require  'vendor/miaoxing/plugin/resources/stubs/test.php';
        $content = ob_get_clean();
        file_put_contents($file, $content);
        chmod($file, 0777);
    }

    protected function createDefinition()
    {
        $this->addArgument('plugin');
    }

    /**
     * @param string $message
     */
    protected function writeln($message)
    {
        fwrite(STDERR, $message . "\n");
    }
}
