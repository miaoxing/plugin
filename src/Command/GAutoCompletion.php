<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\BasePlugin;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \PluginMixin
 * @mixin \ClassMapMixin
 */
class GAutoCompletion extends BaseCommand
{
    protected static $defaultName = 'g:auto-completion';

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setDescription('Generate code auto completion for specified plugin')
            ->addArgument('plugin-id', InputArgument::REQUIRED, 'The id of plugin');
    }

    /**
     * @return int
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function handle()
    {
        $plugin = $this->plugin->getOneById($this->input->getArgument('plugin-id'));

        // NOTE: 需生成两个文件，services.php 里的类才能正确跳转到源文件
        $this->generateServices($plugin);
        $this->generateStaticCalls($plugin);

        return $this->suc('创建成功');
    }

    /**
     * Generate services' auto completion
     *
     * Including
     * 1. Mixin classes
     * 2. Function calls, that is wei()->xxx
     * 3. Global variables
     *
     * @param BasePlugin $plugin
     * @throws \ReflectionException
     */
    protected function generateServices(BasePlugin $plugin)
    {
        $services = $this->getServerMap($plugin);
        $content = "<?php\n\n";
        $autoComplete = '';

        foreach ($services as $name => $class) {
            // 使用 @property 和 @method，PHPStorm 会识别出是动态调用，加粗调用的代码
            $docBlock = rtrim($this->generateDocBlock($name, $class));
            $className = ucfirst($name) . 'Mixin';
            $content .= $this->generateClass($className, $docBlock) . "\n";

            $autoComplete .= ' * @mixin ' . $className . "\n";
        }

        $content .= $this->generateClass('AutoCompletion', rtrim($autoComplete));
        $content .= <<<PHP

/**
 * @return AutoCompletion
 */
function wei()
{
    return new AutoCompletion;
}


PHP;

        $content .= $this->generateViewVars($services);

        $this->createFile($plugin->getBasePath() . '/docs/auto-completion.php', $content);
    }

    /**
     * Generate static calls code completion
     *
     * @param BasePlugin $plugin
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function generateStaticCalls(BasePlugin $plugin)
    {
        $dir = $plugin->getBasePath();
        $services = $this->classMap->generate($dir . '/src', '/Service/*.php', 'Service');

        $file = new PhpFile();
        $printer = new PsrPrinter;
        $content = '';

        foreach ($services as $name => $serviceClass) {
            $refClass = new ReflectionClass($serviceClass);

            $namespace = $file->addNamespace($refClass->getNamespaceName());

            $class = new ClassType($refClass->getShortName());

            $staticClass = clone $class;

            $methods = [];
            $staticMethods = [];
            foreach ($refClass->getMethods(ReflectionMethod::IS_PROTECTED) as $refMethod) {
                if ($this->isApi($refMethod)) {
                    // NOTE: 使用注释，PHPStorm 也不会识别为动态调用
                    $method = Method::from([$serviceClass, $refMethod->getName()])->setPublic();

                    $methods[] = $method;
                    $staticMethods[] = (clone $method)->setStatic();
                }
            }
            $class->setMethods($methods);
            $staticClass->setMethods($staticMethods);

            if ($methods) {
                if ($content) {
                    $content .= "\n";
                }
                $content .= $printer->printClass($class, $namespace);
            }
            if ($staticMethods) {
                $content .= "\nif (0) {\n" . $this->intent(rtrim($printer->printClass($staticClass,
                        $namespace))) . "\n}\n";
            }
        }

        if (!$content) {
            $this->suc('API method not found!');
            return;
        }

        $content = $printer->printFile($file) . "\n" . $content;

        $this->createFile($plugin->getBasePath() . '/docs/auto-completion-static.php', $content);
    }

    /**
     * @param string $name
     * @param string $class
     * @return string
     * @throws \ReflectionException
     */
    protected function generateDocBlock(string $name, string $class)
    {
        $docBlock = '';
        $ref = new ReflectionClass($class);
        $docName = $this->getDocCommentTitle($ref->getDocComment());

        $docBlock .= rtrim(sprintf(' * @property    %s $%s %s', $class, $name, $docName)) . "\n";

        if (method_exists($class, '__invoke')) {
            $method = $ref->getMethod('__invoke');
            $return = $this->getMethodReturn($ref, $method) ?: 'mixed';
            $methodName = $this->getDocCommentTitle($method->getDocComment()) ?: '';

            $params = $this->geParam($method);

            $docBlock .= rtrim(sprintf(' * @method      %s %s(%s) %s', $return, $name, $params, $methodName));
            $docBlock .= "\n";
        }

        return $docBlock;
    }

    protected function generateViewVars($serviceMap)
    {
        $var = '';
        foreach ($serviceMap as $name => $class) {
            if ($var) {
                $var .= "\n";
            }

            // 排除 wei()->model 服务
            $isModel = $this->wei->isEndsWith($name, 'Model', true);
            if ($isModel) {
                $varName = substr($name, 0, -5);
            } else {
                $varName = $name;
            }

            $var .= sprintf('/** @var %s $%s */' . "\n", $class, $name);
            $var .= sprintf('$%s = wei()->%s%s;' . "\n", $varName, $name, $isModel ? '()' : '');

            if ($isModel) {
                $var .= "\n";
                $var .= sprintf('/** @var %s|%s[] $%ss */' . "\n", $class, $class, $name);
                $var .= sprintf('$%ss = wei()->%s();' . "\n", $varName, $name);
            }
        }

        return $var;
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
        $className = $class->getName();

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

    protected function createFile($file, $content)
    {
        $this->suc('生成文件 ' . $file);
        $this->createDir(dirname($file));
        file_put_contents($file, $content);
        chmod($file, 0777);
    }

    protected function createDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
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

    protected function generateClass($class, $comment)
    {
        return <<<PHP
/**
$comment
 */
class $class {
}

PHP;
    }
}
