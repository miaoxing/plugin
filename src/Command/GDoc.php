<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\BasePlugin;
use Miaoxing\Services\Service\Cli;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputArgument;

class GDoc extends BaseCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Generate doc for specified plugin')
            ->addArgument('plugin-id', InputArgument::REQUIRED, 'The id of plugin');
    }

    /**
     * Executes the current command
     *
     * @return int
     */
    protected function handle()
    {
        $plugin = $this->plugin->getOneById($this->input->getArgument('plugin-id'));

        $this->generateAutoComplete($plugin);
        $this->generateType($plugin);

        return $this->suc('创建成功');
    }

    protected function generateAutoComplete($plugin)
    {
        $file = $this->getDocFile($plugin);
        $this->createDir(dirname($file));

        list($namespace, $class) = $this->getDocClass($plugin);

        $serviceMap = $this->getServerMap($plugin);
        $docBlock = $this->generateDocBlock($serviceMap);
        $viewVars = $this->generateViewVars($serviceMap);

        $this->createFile($file, $namespace, $class, $docBlock, $viewVars);
    }

    protected function generateViewVars($serviceMap)
    {
        $var = '';
        foreach ($serviceMap as $name => $class) {
            if ($var) {
                $var .= "\n";
            }

            $isModel = $this->wei->isEndsWith($name, 'Model');
            if ($isModel) {
                $varName = substr($name, 0, -5);
            } else {
                $varName = $name;
            }

            $var .= sprintf('    /** @var %s $%s */' . "\n", $class, $name);
            $var .= sprintf('    $%s = wei()->%s%s;' . "\n", $varName, $name, $isModel ? '()' : '');

            if ($isModel) {
                $var .= "\n";
                $var .= sprintf('    /** @var %s|%s[] $%ss */' . "\n", $class, $class, $name);
                $var .= sprintf('    $%ss = wei()->%s();' . "\n", $varName, $name);
            }
        }

        return $var;
    }

    protected function generateDocBlock(array $serviceMap)
    {
        $docBlock = '';
        foreach ($serviceMap as $name => $class) {
            // 留空一行
            if ($docBlock) {
                $docBlock .= "     *\n";
            }

            $ref = new ReflectionClass($class);
            $docName = $this->getDocCommentTitle($ref->getDocComment());

            $docBlock .= rtrim(sprintf('     * @property    \\%s $%s %s', $class, $name, $docName)) . "\n";

            if (method_exists($class, '__invoke')) {
                $method = $ref->getMethod('__invoke');
                $return = $this->getMethodReturn($ref, $method) ?: 'mixed';
                $methodName = $this->getDocCommentTitle($method->getDocComment()) ?: '';

                $params = $this->geParam($method);

                $docBlock .= rtrim(sprintf('     * @method      %s %s(%s) %s', $return, $name, $params, $methodName));
                $docBlock .= "\n";
            }
        }

        return $docBlock;
    }

    protected function geParam(ReflectionMethod $method)
    {
        $params = $method->getParameters();
        if (!$params) {
            return '';
        }

        $string = '';
        foreach ($params as $param) {
            if ($string) {
                $string .= ', ';
            }

            $string .= '$' . $param->getName();
            if ($param->isDefaultValueAvailable()) {
                $string .= ' = ' . $this->convertParamValueToString($param->getDefaultValue());
            }
        }

        return $string;
    }

    protected function convertParamValueToString($value)
    {
        switch (gettype($value)) {
            case 'NULL':
                return 'null';

            case 'array':
                return '[]';

            default:
                return var_export($value, true);
        }
    }

    protected function getMethodReturn(ReflectionClass $class, ReflectionMethod $method)
    {
        $doc = $method->getDocComment();
        preg_match('/@return (.+?)\n/', $doc, $matches);
        if (!$matches) {
            return false;
        }

        $return = $matches[1];
        $className = '\\' . $class->getName();

        $return = str_replace([
            'BaseModel',
            '$this',
        ], [
            $className,
            $className,
        ], $return);

        // 忽略空格后面的辅助说明
        if ($return) {
            $return = explode(' ', $return)[0];
        }

        return $return ?: false;
    }

    protected function getDocCommentTitle($docComment)
    {
        /**
         * Xxx
         *
         * xxx
         * xxx
         *
         * @xxx xx
         */
        // 如上注释,返回 Xxx
        preg_match('#\* ([^@]+?)\n#is', $docComment, $matches);
        if ($matches) {
            return $matches[1];
        }

        return false;
    }

    protected function getDocFile(BasePlugin $plugin)
    {
        // Remove vendorName/packageName
        $parts = explode('\\', get_class($plugin));
        array_shift($parts);
        array_shift($parts);

        $file = $plugin->getBasePath() . '/docs/AutoComplete.php';

        return $file;
    }

    /**
     * 类名如 Miaoxing\Xxx\Plugin
     * 转换为 [MiaoxingDoc\Xxx, AutoComplete]
     *
     * @param BasePlugin $plugin
     * @return array
     */
    protected function getDocClass(BasePlugin $plugin)
    {
        $class = get_class($plugin);
        $parts = explode('\\', $class);

        return [$parts[0] . 'Doc\\' . $parts[1], 'AutoComplete'];
    }

    protected function createDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
    }

    protected function createFile($file, $namespace, $class, $docBlock, $viewVars)
    {
        $this->suc('生成文件 ' . $file);

        ob_start();
        require $this->plugin->getById('plugin')->getBasePath() . '/resources/stubs/doc.php';
        $content = ob_get_clean();

        file_put_contents($file, $content);
        chmod($file, 0777);
    }

    /**
     * @param BasePlugin $plugin
     * @return array
     */
    protected function getServerMap(BasePlugin $plugin)
    {
        $basePath = $plugin->getBasePath() . '/src';

        return wei()->classMap->generate([$basePath], '/Service/*.php', 'Service', false);
    }

    public function generateType(BasePlugin $plugin)
    {
        $dir = $plugin->getBasePath();
        $services = wei()->classMap->generate($dir . '/src', '/Service/*.php', 'Service');

        $file = new PhpFile();
        $printer = new PsrPrinter;
        $content = '';

        foreach ($services as $name => $serviceClass) {
            $refClass = new ReflectionClass($serviceClass);

            $file->addNamespace($refClass->getNamespaceName());

            $class = new ClassType($refClass->getShortName());
            $class->setInterface();

            $staticClass = clone $class;

            $methods = [];
            $staticMethods = [];
            foreach ($refClass->getMethods(ReflectionMethod::IS_PROTECTED) as $refMethod) {
                if ($this->isApi($refMethod)) {
                    $method = Method::from([$serviceClass, $refMethod->getName()])
                        ->setBody(null)
                        ->setPublic();

                    $methods[] = $method;
                    $staticMethods[] = (clone $method)->setStatic();
                }
            }
            $class->setMethods($methods);
            $staticClass->setMethods($staticMethods);

            if ($methods) {
                $content .= $printer->printClass($class);
            }
            if ($staticMethods) {
                $content .= "\nif (0) {\n" . $this->intent(rtrim($printer->printClass($staticClass))) . "\n}\n";
            }
        }

        if (!$content) {
            $this->suc('API method not found!');
            return;
        }

        $content = $printer->printFile($file) . "\n" . $content;

        $this->createDir($dir . '/docs');
        $file = $dir . '/docs/type.php';
        file_put_contents($file, $content);
        $this->suc('生成文件 ' . $file);
    }

    protected function intent($content, $space = '    ')
    {
        $array = [];
        foreach (explode("\n", $content) as $line) {
            $array[] = $space . $line;
        }
        return implode("\n", $array);
    }

    protected function isApi(ReflectionMethod $method)
    {
        return strpos($method->getDocComment(), '* @api');
    }
}
