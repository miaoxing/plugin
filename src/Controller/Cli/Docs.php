<?php

namespace Miaoxing\Plugin\Controller\Cli;

use miaoxing\plugin\BaseController;
use miaoxing\plugin\BasePlugin;
use Miaoxing\Plugin\CliDefinition;
use Miaoxing\Plugin\Service\Cli;
use ReflectionClass;
use ReflectionMethod;

/**
 * @property Cli $cli
 */
class Docs extends BaseController
{
    use CliDefinition;

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

        $file = $this->getDocFile($plugin);
        $this->createDir(dirname($file));

        list($namespace, $class) = $this->getDocClass($plugin);

        $serviceMap = $this->getServerMap($plugin);
        $docBlock = $this->generateDocBlock($serviceMap);

        $this->createFile($file, $namespace, $class, $docBlock);

        return $this->suc();
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

            $docBlock .= sprintf("     * @property    \\%s \$%s %s\n", $ref->getName(), $name, $docName);

            if (method_exists($class, '__invoke')) {
                $method = $ref->getMethod('__invoke');
                $return = $this->getMethodReturn($ref, $method);
                $methodName = $this->getDocCommentTitle($method->getDocComment()) ?: '';

                $docBlock .= sprintf("     * @method      %s %s() %s\n", $return, $name, $methodName);
            }
        }

        return $docBlock;
    }

    protected function getMethodReturn(ReflectionClass $class, ReflectionMethod $method)
    {
        $doc = $method->getDocComment();
        preg_match('/@return (.+?)\n/', $doc, $matches);
        if (!$matches) {
            return false;
        }

        if ($matches[1] == 'BaseModel|BaseModel[]') {
            $className = $class->getName();
            return '\\' . $className . '|\\' . $className . '[]';
        }

        return $matches[1];
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

    protected function createFile($file, $namespace, $class, $docBlock)
    {
        $this->writeln('生成文件 ' . $this->cli->success($file));

        ob_start();
        require $this->plugin->getById('plugin')->getBasePath() .'/resources/stubs/doc.php';
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

    /**
     * @param BasePlugin $plugin
     * @return array
     */
    protected function getServerMap(BasePlugin $plugin)
    {
        $basePath = $plugin->getBasePath() . '/src';

        return $this->plugin->generateClassMap([$basePath], '/Service/*.php', 'Service');
    }
}
