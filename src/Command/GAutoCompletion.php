<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\BasePlugin;
use Miaoxing\Services\Service\ClassMap;
use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 生成自动完成的代码文件
 *
 * 可行方案
 * 1. FILE_MODE_SINGLE + excludeParentMethods=false
 * 2. FILE_MODE_BY_TYPE + excludeParentMethods=true（推荐，生成的代码少）
 *
 * @mixin \PluginMixin
 * @mixin \ClassMapMixin
 * @see StaticCallTest
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
    protected $excludeParentMethods = true;

    /**
     * @var int
     */
    protected $fileMode = self::FILE_MODE_BY_TYPE;

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
        $id = $this->getArgument('plugin-id');
        if ($id === 'wei') {
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

        $files = glob($path . '/Validator/*.php');
        foreach ($files as $file) {
            $name = basename($file, '.php');
            // TODO Null 类 php7 不支持
            if ($name === 'Null') {
                continue;
            }
            $services['is' . $name] = 'Wei\\Validator\\' . $name;
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
        $content .= <<<PHP

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

    /**
     * Generate static calls code completion
     *
     * @param BasePlugin $plugin
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function generateStaticCalls(array $services, string $path)
    {
        $file = new PhpFile();
        $printer = new PsrPrinter;

        $statics = [];
        $dynamics = [];

        foreach ($services as $name => $serviceClass) {
            // 忽略 trait
            if (!class_exists($serviceClass)) {
                continue;
            }

            $refClass = new ReflectionClass($serviceClass);

            $namespace = $file->addNamespace($refClass->getNamespaceName());

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

                    $method->setComment(str_replace('@svc', $see . $refMethod->getName(), $method->getComment()));

                    $methods[] = $method;
                    $staticMethods[] = (clone $method)->setStatic();
                }
            }

            if ($this->generateEmptyClass || $staticMethods) {
                $staticClass->setMethods($staticMethods);
                $statics[$name] = $printer->printClass($staticClass, $namespace);
            }
            if ($this->generateEmptyClass || $methods) {
                // NOTE: 分多个文件反而出现第二，三级的子类(例如AppModel)没有代码提示，魔术方法识别失败等问题
                $class->setMethods($methods);
                $dynamics[$name] = $printer->printClass($class, $namespace);
            }
        }

        if (!$statics && !$dynamics) {
            $this->suc('API method not found!');
            return;
        }

        $header = $printer->printFile($file) . "\n";
        switch ($this->fileMode) {
            case self::FILE_MODE_SINGLE:
                $content = $header . implode("\n", $statics);
                $content .= "\nif (0) {\n" . $this->intent(rtrim(implode("\n", $dynamics))) . "\n}\n";
                $this->createFile($path . '/docs/auto-completion-static.php', $content);
                break;

            case self::FILE_MODE_BY_TYPE:
                $statics = $header . implode("\n", $statics);
                $this->createFile($path . '/docs/auto-completion-static.php', $statics);

                $dynamics = $header . implode("\n", $dynamics);
                $this->createFile($path . '/docs/auto-completion-dynamic.php', $dynamics);
                break;

            case self::FILE_MODE_BY_CLASS:
                foreach ($statics as $name => $content) {
                    $this->createFile($path . '/docs/auto-completion-static-' . $name . '.php', $header . $content);
                }
                foreach ($dynamics as $name => $content) {
                    $this->createFile($path . '/docs/auto-completion-dynamic-' . $name . '.php', $header . $content);
                }
                break;
        }
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
}
