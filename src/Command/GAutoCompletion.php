<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\BasePlugin;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 生成自动完成的代码文件
 *
 * 可行方案
 * 1. FILE_MODE_SINGLE + excludeParentMethods=false (推荐)
 * - PHPStorm 识别稳定
 * - 生成代码多
 *
 * 2. FILE_MODE_BY_TYPE + excludeParentMethods=true
 * - PHPStorm 识别不稳定，多次重启后能识别到
 * - 生成代码少
 *
 * 3. FILE_MODE_BY_CLASS
 * - 暂无区别，未实现
 *
 * @mixin \PluginMixin
 * @mixin \ClassMapMixin
 * @mixin \StrMixin
 * @see StaticCallTest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GAutoCompletion extends BaseCommand
{
    /**
     * 所有的类生成一个文件
     */
    public const FILE_MODE_SINGLE = 1;

    /**
     * 生成两个文件，一个存放静态方法，一个存放动态方法
     */
    public const FILE_MODE_BY_TYPE = 2;

    /**
     * 每个类生成一个文件
     */
    public const FILE_MODE_BY_CLASS = 3;

    protected static $defaultName = 'g:auto-completion';

    /**
     * @var bool
     */
    protected $generateEmptyClass = true;

    /**
     * @var bool
     */
    protected $excludeParentMethods = false;

    /**
     * @var bool
     */
    protected $addNoinspectionComment = false;

    /**
     * @var int
     */
    protected $fileMode = self::FILE_MODE_SINGLE;

    /**
     * Generate static calls code completion
     *
     * @param array $services
     * @param string $path
     * @throws \ReflectionException
     */
    public function generateStaticCalls(array $services, string $path)
    {
        $printer = new PsrPrinter();
        $staticFile = new PhpFile();
        $dynamicFile = new PhpFile();

        if ($this->addNoinspectionComment) {
            $staticFile->addComment('@noinspection PhpDocSignatureInspection')
                ->addComment('@noinspection PhpFullyQualifiedNameUsageInspection')
                ->addComment('@noinspection PhpInconsistentReturnPointsInspection');
        }

        foreach ($services as $name => $serviceClass) {
            // 忽略 trait
            if (!class_exists($serviceClass)) {
                continue;
            }

            $refClass = new ReflectionClass($serviceClass);

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
            $see = '@see ' . $refClass->getShortName() . '::';
            foreach ($refClass->getMethods(ReflectionMethod::IS_PROTECTED) as $refMethod) {
                // NOTE: 单文件下，如果排除了父类方法，第二级的子类(例如AppModel)没有代码提示
                if ($this->excludeParentMethods && $refMethod->getDeclaringClass()->getName() !== $serviceClass) {
                    continue;
                }

                if ($this->isApi($refMethod)) {
                    // NOTE: 使用注释，PHPStorm 也不会识别为动态调用
                    $method = Method::from([$serviceClass, $refMethod->getName()])->setPublic();

                    $see = '@see ' . $refMethod->getDeclaringClass()->getShortName() . '::' . $refMethod->getName();
                    $method->setComment(str_replace('@svc', $see, $method->getComment()));

                    $methods[] = $method;
                    $staticMethods[] = (clone $method)->setStatic();
                }
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

        switch ($this->fileMode) {
            default:
            case self::FILE_MODE_SINGLE:
                $this->writeSingle($printer, $staticFile, $dynamicFile, $path);
                break;

            case self::FILE_MODE_BY_TYPE:
                $this->writeByType($printer, $staticFile, $dynamicFile, $path);
                break;

            case self::FILE_MODE_BY_CLASS:
                $this->writeByClass($printer, $staticFile, $dynamicFile, $path);
                break;
        }
    }

    /**
     * {@inheritdoc}
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
        $id = $this->getArgument('plugin-id');
        if ('wei' === $id) {
            [$services, $path, $generateViewVars] = $this->getWeiConfig();
        } else {
            $plugin = $this->plugin->getOneById($this->input->getArgument('plugin-id'));
            $path = $plugin->getBasePath();
            $services = $this->getServerMap($plugin);
            $generateViewVars = true;
        }

        // NOTE: 需生成两个文件，services.php 里的类才能正确跳转到源文件
        $this->generateServices($services, $path, $generateViewVars);
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
            $services[lcfirst($name)] = 'Wei\\' . $name;
        }

        foreach ($services as $name => $class) {
            if ((new ReflectionClass($class))->isAbstract()) {
                unset($services[$name]);
            }
        }

        return [$services, 'packages/wei', false];
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
     * @param bool $generateViewVars
     * @throws \ReflectionException
     */
    protected function generateServices(array $services, string $path, bool $generateViewVars = true)
    {
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
        $content .= <<<'PHP'

/**
 * @return AutoCompletion
 */
function wei()
{
    return new AutoCompletion;
}


PHP;

        $generateViewVars && $content .= $this->generateViewVars($services);

        $this->createFile($path . '/docs/auto-completion.php', $content);
    }

    protected function writeSingle(PsrPrinter $printer, PhpFile $staticFile, PhpFile $dynamicFile, string $path)
    {
        $statics = $printer->printFile($staticFile);
        $dynamics = $printer->printFile($dynamicFile);

        // Remove first (<?php\n) line
        $dynamics = substr($dynamics, strpos($dynamics, "\n") + 1);

        // Wrap `if (0) ` outside class definition
        $index = 0;
        $dynamics = preg_replace_callback('/namespace (.+?)\n/mi', function ($matches) use (&$index) {
            ++$index;
            $prefix = 1 === $index ? '' : "\n}\n";
            return $prefix . $matches[0] . "\nif (0) {";
        }, $dynamics);
        $dynamics .= "}\n";

        $content = $statics . $dynamics;
        $this->createFile($path . '/docs/auto-completion-static.php', $content);

        $file = $path . '/docs/auto-completion-dynamic.php';
        if (is_file($file)) {
            unlink($file);
        }
    }

    protected function writeByType(PsrPrinter $printer, PhpFile $staticFile, PhpFile $dynamicFile, string $path)
    {
        $statics = $printer->printFile($staticFile);
        $this->createFile($path . '/docs/auto-completion-static.php', $statics);

        $dynamics = $printer->printFile($dynamicFile);
        $this->createFile($path . '/docs/auto-completion-dynamic.php', $dynamics);
    }

    protected function writeByClass(PsrPrinter $printer, PhpFile $staticFile, PhpFile $dynamicFile, string $path)
    {
        throw new \RuntimeException('Not supported yet');
//        $header = $printer->printFile($staticFile) . "\n";
//        foreach ($statics as $name => $content) {
//            $this->createFile($path . '/docs/auto-completion-static-' . $name . '.php', $header . $content);
//        }
//        foreach ($dynamics as $name => $content) {
//            $this->createFile($path . '/docs/auto-completion-dynamic-' . $name . '.php', $header . $content);
//        }
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

            // 排除模型服务
            $isModel = $this->wei->isEndsWith($name, 'Model', true);
            if ($isModel) {
                $varName = substr($name, 0, -5);
            } else {
                $varName = $name;
            }

            $var .= sprintf('/** @var %s $%s */' . "\n", $class, $varName);
            $var .= sprintf('$%s = wei()->%s;' . "\n", $varName, $name);

            if ($isModel) {
                $varName = $this->str->pluralize($varName);
                $var .= "\n";
                $var .= sprintf('/** @var %s|%s[] $%s */' . "\n", $class, $class, $varName);
                $var .= sprintf('$%s = wei()->%s();' . "\n", $varName, $name);
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
        return strpos($method->getDocComment(), '* @svc');
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

    private function addValidatorMethods(array $services, PhpFile $staticFile, PhpFile $dynamicFile)
    {
        $validators = [];
        foreach ($services as $name => $class) {
            if ('is' === substr($name, 0, 2)) {
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

            // 移除 $input 参数
            $parameters = $dynamicMethod->getParameters();
            array_shift($parameters);

            if ($class::BASIC_TYPE) {
                // 加上 name 和 label
                $nameParameter = new Parameter('name');
                $nameParameter->setDefaultValue(null);

                $labelParameter = new Parameter('label');
                $labelParameter->setType('string');
                $labelParameter->setDefaultValue(null);
                array_unshift($parameters, $nameParameter, $labelParameter);
            }

            $dynamicMethod->setParameters($parameters);

            $dynamicMethod->setComment('@return $this');
            $dynamicMethod->addComment('@see \\' . $class . '::__invoke');

            $methods[] = $dynamicMethod;
            $staticMethods[] = (clone $dynamicMethod)->setStatic();

            $methods[] = $dynamicMethod->cloneWithName('not' . $name);
            $staticMethods[] = $dynamicMethod->cloneWithName('not' . $name)->setStatic();
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
