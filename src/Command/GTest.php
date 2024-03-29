<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\BasePlugin;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \PluginMixin
 */
class GTest extends BaseCommand
{
    use PluginIdTrait;

    protected function configure()
    {
        $this->addArgument('plugin-id', InputArgument::OPTIONAL, 'The id of plugin');
    }

    protected function handle()
    {
        $id = $this->getPluginId();

        $plugin = wei()->plugin->getOneById($id);

        $classes = $plugin->getControllerMap();
        foreach ($classes as $class) {
            $testFile = $this->getTestFile($plugin, $class);
            if (is_file($testFile)) {
                $this->suc('文件已存在 ' . $testFile);
                continue;
            }

            $this->createDir(dirname($testFile));

            list($namespace, $class) = $this->getTestClass($class);

            $this->createFile($testFile, $namespace, $class);
        }

        return $this->suc('创建成功');
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
        $this->suc('生成文件 ' . $file);

        ob_start();
        require $this->plugin->getById('plugin')->getBasePath() . '/stubs/test.php';
        $content = ob_get_clean();
        file_put_contents($file, $content);
        chmod($file, 0777);
    }
}
