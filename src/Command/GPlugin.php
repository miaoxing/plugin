<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\BasePlugin;
use Miaoxing\Plugin\Service\Fs;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \PluginMixin
 * @mixin \StrMixin
 */
class GPlugin extends BaseCommand
{
    protected function configure()
    {
        $this->setDescription('Generate a plugin')
            ->addArgument('id', InputArgument::REQUIRED, 'The id of the plugin')
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the plugin', '');
    }

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        $dashId = $this->str->dash($this->getArgument('id'));
        $camelId = $this->str->camel($dashId);
        $pascalId = ucfirst($camelId);

        $name = $this->getArgument('name');

        if ($this->plugin->has($dashId)) {
            return $this->err(sprintf('Plugin "%s" exists', $dashId));
        }

        $printer = new PsrPrinter();
        $phpFile = new PhpFile();

        $class = $phpFile->addClass('Miaoxing\\' . $pascalId . '\\' . $pascalId . 'Plugin');
        $class->addExtend(BasePlugin::class);
        $class->addProperty('name', $name)->setProtected();
        $class->addProperty('description', '')->setProtected();

        $file = 'plugins/' . $dashId . '/src/' . $pascalId . 'Plugin.php';
        $content = $printer->printFile($phpFile);
        $this->createFile($file, $content);

        $replaces = [
            'composer.json' => [
                ['dash-id', 'PascalId'],
                [$dashId, $pascalId],
            ],
            'package.json' => [
                ['dash-id'],
                [$dashId],
            ],
            'README.md' => [
                ['Name'],
                [$name],
            ],
        ];

        $stubDir = $this->plugin->getById('plugin')->getBasePath() . '/stubs/plugin';
        $targetDir = 'plugins/' . $dashId;
        $this->createFiles($stubDir, $targetDir, $replaces);

        $this->plugin->getConfig(true);

        return $this->suc('创建成功');
    }

    protected function createFiles($stubDir, $targetDir, $replaces)
    {
        Fs::ensureDir($targetDir);
        $files = array_diff(scandir($stubDir), ['..', '.']);
        foreach ($files as $file) {
            $source = $stubDir . '/' . $file;
            $target = $targetDir . '/' . $file;
            if (isset($replaces[$file])) {
                $content = file_get_contents($source);
                $content = str_replace($replaces[$file][0], $replaces[$file][1], $content);
                $this->createFile($target, $content);
            } else {
                $this->suc('生成文件 ' . $target);
                copy($source, $target);
            }
        }
    }

    protected function createFile($file, $content)
    {
        $this->suc('生成文件 ' . $file);

        Fs::ensureDir(dirname($file));

        file_put_contents($file, $content);
        chmod($file, 0777);
    }
}
