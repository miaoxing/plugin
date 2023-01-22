<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\Service\Fs;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\Console\Input\InputArgument;
use Wei\BaseModel;

/**
 * @mixin \StrMixin
 */
class GRelation extends BaseCommand
{
    protected function configure()
    {
        $this->setDescription('Generate relation traits')
            ->addArgument('model', InputArgument::REQUIRED, 'The name of the model')
            ->addArgument('belongs-to-many', InputArgument::OPTIONAL, 'Whether generate belongs to many trait', false);
    }

    protected function handle()
    {
        $name = $this->getArgument('model');
        if (!$this->wei->has($name)) {
            return $this->err(sprintf('模型 %s 不存在', $name));
        }

        $class = $this->wei->getClass($name);
        if (!is_subclass_of($class, BaseModel::class)) {
            return $this->err(sprintf('类 %s 不是模型类', $class));
        }

        $this->createTrait($name, 'belongsTo');
        $this->createTrait($name, 'hasOne');
        $this->createTrait($name, 'hasMany', true);
        $this->getArgument('belongs-to-many') && $this->createTrait($name, 'belongsToMany', true);
    }

    protected function createTrait($name, $relation, $isPluralize = false)
    {
        // Prepare variables
        $methodName = substr($name, 0, -strlen('Model'));
        if ($isPluralize) {
            $methodName = $this->str->pluralize($methodName);
        }

        $class = $this->wei->getClass($name);
        $pluginNamespace = explode('\\', $class)[1];
        $classBaseName = ucfirst($name);
        $fileName = ucfirst($relation) . ucfirst($methodName) . 'Trait';

        // Generate file
        $phpFile = new PhpFile();
        $namespace = $phpFile->addNamespace('Miaoxing\\' . $pluginNamespace . '\\Model');
        $namespace->addUse($class);

        // Generate trait
        $trait = $namespace->addTrait($fileName);
        $propertyType = $isPluralize ? ($classBaseName . '|' . $classBaseName . '[]') : $classBaseName;
        $trait->setComment('@property ' . $propertyType . ' $' . $methodName);
        $trait->addMethod($methodName)
            ->setBody('return $this->' . $relation . '(' . $classBaseName . '::class);')
            ->setReturnType($class);

        // Write to file
        $pluginId = $this->str->dash($pluginNamespace);
        $file = 'plugins/' . $pluginId . '/src/Model/' . $fileName . '.php';

        $printer = new PsrPrinter();
        $content = $printer->printFile($phpFile);

        $this->createFile($file, $content);
    }

    protected function createFile($file, $content)
    {
        $this->suc('生成文件 ' . $file);

        Fs::ensureDir(dirname($file));

        file_put_contents($file, $content);
        chmod($file, 0777);
    }
}
