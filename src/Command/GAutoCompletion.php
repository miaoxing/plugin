<?php

namespace Miaoxing\Plugin\Command;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\Console\Input\InputArgument;
use Wei\BaseValidator;

/**
 * 生成自动完成的代码文件
 *
 * 可行方案
 * 1. 静态和动态方法合并在一起 + excludeParentMethods=false (推荐)
 * - PHPStorm 识别稳定
 * - 生成代码多
 *
 * 2. 静态和动态方法分开为独立文件 + excludeParentMethods=true
 * - PHPStorm 识别不稳定，多次重启后能识别到
 * - 生成代码少
 *
 * 3. 每个类生成一个文件
 * - 暂无区别
 * - 生成文件过多
 *
 * @mixin \PluginPropMixin
 * @mixin \ClassMapMixin
 * @mixin \StrMixin
 * @see StaticCallTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GAutoCompletion extends BaseCommand
{
    protected static $defaultName = 'g:auto-completion';

    /**
     * @var bool
     */
    protected bool $generateEmptyClass = false;

    /**
     * @var bool
     */
    protected bool $excludeParentMethods = false;

    /**
     * @var bool
     */
    protected bool $addNoinspectionComment = false;

    /**
     * 每个文件最多的方法数
     *
     * 生成文件过大 PHPStorm 不会解析
     *
     * @var int
     */
    protected int $maxMethodCount = 3000;

    /**
     * Generate static calls code completion
     *
     * @param array $services
     * @param string $path
     * @throws \ReflectionException
     */
    public function generateStaticCalls(array $services, string $path)
    {
        $staticFiles = [];
        $dynamicFiles = [];

        $printer = new PsrPrinter();

        $staticFile = new PhpFile();
        $staticFiles[] = $staticFile;

        $dynamicFile = new PhpFile();
        $dynamicFiles[] = $dynamicFile;

        if ($this->addNoinspectionComment) {
            $staticFile->addComment('@noinspection PhpDocSignatureInspection')
                ->addComment('@noinspection PhpFullyQualifiedNameUsageInspection')
                ->addComment('@noinspection PhpInconsistentReturnPointsInspection');
        }

        $methodCount = 0;
        foreach ($services as $serviceClass) {
            // 忽略 trait
            if (!class_exists($serviceClass)) {
                continue;
            }

            if ($methodCount >= $this->maxMethodCount) {
                $methodCount = 0;

                $staticFile = new PhpFile();
                $staticFiles[] = $staticFile;

                $dynamicFile = new PhpFile();
                $dynamicFiles[] = $dynamicFile;
            }

            $refClass = new \ReflectionClass($serviceClass);

            $staticNamespace = $staticFile->addNamespace($refClass->getNamespaceName());
            $dynamicNamespace = $dynamicFile->addNamespace($refClass->getNamespaceName());

            $class = new ClassType($refClass->getShortName());
            // NOTE: 如果增加了继承，Service目录之外的子类没有代码提示（如果还无效不断重启直到生效）
            if ($this->excludeParentMethods && $parent = $refClass->getParentClass()) {
                $class->addExtend($parent->getName());
            }

            $staticClass = clone $class;

            $methods = [];
            $staticMethods = [];
            foreach ($refClass->getMethods(\ReflectionMethod::IS_PROTECTED) as $refMethod) {
                // NOTE: 单文件下，如果排除了父类方法，第二级的子类(例如AppModel)没有代码提示
                if ($this->excludeParentMethods && $refMethod->getDeclaringClass()->getName() !== $serviceClass) {
                    continue;
                }

                if (!$this->isApi($refMethod)) {
                    continue;
                }

                // NOTE: 使用注释，PHPStorm 也不会识别为动态调用
                $method = Method::from([$serviceClass, $refMethod->getName()])->setPublic();

                $see = '@see ' . $refMethod->getDeclaringClass()->getShortName() . '::' . $refMethod->getName();
                $method->setComment(str_replace('@svc', $see, $method->getComment()));

                $methods[] = $method;
                $staticMethods[] = (clone $method)->setStatic();
                ++$methodCount;
            }

            if ($this->generateEmptyClass || $staticMethods) {
                $staticClass->setMethods($staticMethods);
                $staticNamespace->add($staticClass);
            }
            if ($this->generateEmptyClass || $methods) {
                // NOTE: 分多个文件反而出现第二，三级的子类(例如AppModel)没有代码提示，魔术方法识别失败等问题
                $class->setMethods($methods);
                $dynamicNamespace->add($class);
            }
        }

        $this->addValidatorMethods($services, $staticFile, $dynamicFile);

        if (!isset($staticNamespace) || !$staticNamespace->getClasses()) {
            $this->suc('API method not found!');
            return;
        }

        $this->writeFiles($printer, $staticFiles, $dynamicFiles, $path);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setDescription('Generate code auto completion for specified plugin')
            ->addArgument('plugin-id', InputArgument::OPTIONAL, 'The id of plugin');
    }

    /**
     * @return int
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function handle()
    {
        $id = $this->getArgument('plugin-id');
        // @experimental
        if ('wei' === $id) {
            [$services, $path] = $this->getWeiConfig();
        } else {
            $services = $this->wei->getAliases();
            $path = getcwd();
        }

        // NOTE: 需生成两个文件，services.php 里的类才能正确跳转到源文件
        $this->generateServices($services, $path);
        $this->generateStaticCalls($services, $path);

        return $this->suc('创建成功');
    }

    protected function getWeiConfig()
    {
        // TODO
        // 1. ClassMap 服务支持 wei/lib 目录(无类型)
        // 2. wei/wei 增加 psr-4 配置？

        $services = [];
        $path = 'packages/wei/lib';

        $files = glob($path . '/*.php');
        foreach ($files as $file) {
            $name = basename($file, '.php');
            $class = 'Wei\\' . $name;
            if (class_exists($class)) {
                $services[lcfirst($name)] = $class;
            }
        }

        foreach ($services as $name => $class) {
            if ((new \ReflectionClass($class))->isAbstract()) {
                unset($services[$name]);
            }
        }

        return [$services, 'packages/wei'];
    }

    /**
     * Generate services' auto completion
     *
     * Including
     * 1. Mixin classes
     * 2. Function calls, that is wei()->xxx
     * 3. Global variables
     *
     * @param array $services
     * @param string $path
     * @throws \ReflectionException
     */
    protected function generateServices(array $services, string $path)
    {
        $content = "<?php\n\n";
        $autoComplete = '';

        foreach ($services as $name => $class) {
            // 使用 @property 和 @method，PHPStorm 会识别出是动态调用，加粗调用的代码
            $docBlock = rtrim($this->generateDocBlock($name, $class));
            $className = ucfirst($name) . 'Mixin';
            $content .= $this->generateClass($className, $docBlock) . "\n";

            $autoComplete .= ' * @mixin ' . $className . "\n";

            $propDocBlock = rtrim($this->generateDocBlock($name, $class, false));
            $propClassName = ucfirst($name) . 'PropMixin';
            $content .= $this->generateClass($propClassName, $propDocBlock) . "\n";
        }

        $content .= $this->generateClass('AutoCompletion', rtrim($autoComplete));
        $content .= <<<'PHP'

/**
 * @return AutoCompletion|Wei\Wei
 */
function wei()
{
    return new AutoCompletion(func_get_args());
}

PHP;

        $this->createFile($path . '/docs/auto-completion.php', $content);
    }

    protected function writeFiles(PsrPrinter $printer, array $staticFiles, array $dynamicFiles, string $path)
    {
        $this->deleteStaticFiles($path);

        foreach ($staticFiles as $i => $staticFile) {
            $dynamicFile = $dynamicFiles[$i];

            $statics = $printer->printFile($staticFile);
            $dynamics = $printer->printFile($dynamicFile);

            // Remove first (<?php\n) line
            $dynamics = substr($dynamics, strpos($dynamics, "\n") + 1);

            // indent 4 spaces
            $lines = [];
            foreach (explode("\n", $dynamics) as $line) {
                $lines[] = $line ? ('    ' . $line) : '';
            }
            $dynamics = implode("\n", $lines);

            // Wrap `if (0) ` outside class definition
            $index = 0;
            $dynamics = preg_replace_callback('/    namespace (.+?)\n/mi', static function ($matches) use (&$index) {
                ++$index;
                $prefix = 1 === $index ? '' : "\n}\n";
                return $prefix . ltrim($matches[0]) . "\nif (0) {";
            }, $dynamics);
            $dynamics .= "}\n";

            $content = $statics . $dynamics;
            $this->createFile($path . '/docs/auto-completion-static-' . ($i + 1) . '.php', $content);
        }

        $file = $path . '/docs/auto-completion-dynamic.php';
        if (is_file($file)) {
            unlink($file);
        }
    }

    /**
     * @param string $name
     * @param string $class
     * @param mixed $generateInvoke
     * @return string
     * @throws \ReflectionException
     */
    protected function generateDocBlock(string $name, string $class, $generateInvoke = true)
    {
        $docBlock = '';
        $ref = new \ReflectionClass($class);
        $docName = $this->getDocCommentTitle($ref->getDocComment());

        $docBlock .= rtrim(sprintf(' * @property    %s $%s %s', $class, $name, $docName)) . "\n";

        if ($generateInvoke && method_exists($class, '__invoke')) {
            $method = $ref->getMethod('__invoke');
            $return = $this->getMethodReturn($ref, $method) ?: 'mixed';
            $methodName = $this->getDocCommentTitle($method->getDocComment()) ?: '';

            $params = $this->geParam($method);

            $docBlock .= rtrim(sprintf(' * @method      %s %s(%s) %s', $return, $name, $params, $methodName));
            $docBlock .= "\n";
        }

        return $docBlock;
    }

    protected function geParam(\ReflectionMethod $method)
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

    protected function getMethodReturn(\ReflectionClass $class, \ReflectionMethod $method)
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

    /**
     * 返回注释的标题（第一行）
     *
     * @param string $docComment
     * @return bool|mixed
     */
    protected function getDocCommentTitle($docComment)
    {
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

    protected function intent($content, $space = '    ')
    {
        $array = [];
        foreach (explode("\n", $content) as $line) {
            $array[] = $space . $line;
        }
        return implode("\n", $array);
    }

    protected function isApi(\ReflectionMethod $method)
    {
        return strpos($method->getDocComment(), '* @svc');
    }

    protected function generateClass($class, $comment)
    {
        return <<<PHP
/**
$comment
 */
 #[\\AllowDynamicProperties]
class $class
{
}

PHP;
    }

    protected function deleteStaticFiles(string $path)
    {
        $files = glob($path . '/docs/auto-completion-static-*.php');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    private function addValidatorMethods(array $services, PhpFile $staticFile, PhpFile $dynamicFile)
    {
        $validators = [];
        foreach ($services as $name => $class) {
            if ('is' === substr($name, 0, 2) && is_subclass_of($class, BaseValidator::class)) {
                $validators[$name] = $class;
            }
        }
        if (!$validators) {
            return;
        }

        $staticNamespace = $staticFile->addNamespace('Wei');
        $dynamicNamespace = $dynamicFile->addNamespace('Wei');
        $staticClass = $this->getOrAddClass($staticNamespace, 'V');
        $dynamicClass = $this->getOrAddClass($dynamicNamespace, 'V');

        $methods = $dynamicClass->getMethods();
        $staticMethods = $staticClass->getMethods();

        foreach ($validators as $name => $class) {
            $name = substr($name, 2);

            $dynamicMethod = Method::from([$class, '__invoke'])->cloneWithName(lcfirst($name));
            $staticMethod = clone $dynamicMethod;

            // 移除 $input 参数
            $parameters = $dynamicMethod->getParameters();
            array_shift($parameters);

            // 加上 key 和 label
            $nameParameter = new Parameter('key');
            $nameParameter->setDefaultValue(null);

            $labelParameter = new Parameter('label');
            $labelParameter->setType('string');
            $labelParameter->setDefaultValue(null);
            array_unshift($parameters, $nameParameter, $labelParameter);

            $dynamicMethod->setParameters($parameters);

            $dynamicMethod->setComment('@return $this');
            $dynamicMethod->addComment('@see \\' . $class . '::__invoke');

            $staticMethod->setComment('@return $this');
            $staticMethod->addComment('@see \\' . $class . '::__invoke');

            $methods[] = $dynamicMethod;
            $staticMethods[] = $staticMethod->setStatic();

            $methods[] = $dynamicMethod->cloneWithName('not' . $name);
            $staticMethods[] = $staticMethod->cloneWithName('not' . $name);
        }

        $dynamicClass->setMethods($methods);
        $staticClass->setMethods($staticMethods);
    }

    private function getOrAddClass(PhpNamespace $namespace, string $class)
    {
        $classes = $namespace->getClasses();
        return $classes[$class] ?? $namespace->addClass($class);
    }
}
