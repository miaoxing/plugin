<?php

namespace Miaoxing\Plugin\Controller\Cli;

use miaoxing\plugin\BaseController;
use Miaoxing\Plugin\BasePlugin;
use Miaoxing\Plugin\CliDefinition;
use ReflectionClass;
use ReflectionMethod;

class Metadata extends BaseController
{
    use CliDefinition;

    /**
     * @param $req
     * @return array
     * @throws \Exception
     */
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

        $services = $this->plugin->generateClassMap(
            [$plugin->getBasePath() . '/src'],
            '/Service/*Record.php',
            'Service',
            false
        );
        foreach ($services as $name => $class) {
            $uses = $this->classUsesDeep($class);
            $camelCase = isset($uses['Miaoxing\Plugin\Model\CamelCaseTrait']);
            $this->createClass($name, $plugin, $camelCase);
        }

        return $this->suc();
    }

    protected function createClass($model, $plugin, $camelCase)
    {
        $table = wei()->$model->getTable();
        $defaultCasts = wei()->$model->getOption('defaultCasts') ?: [];
        $columns = wei()->db->fetchAll('SHOW FULL COLUMNS FROM ' . $table);

        $casts = [];
        $docBlock = '';
        foreach ($columns as $column) {
            if (isset($defaultCasts[$column['Field']])) {
                $casts[$column['Field']] = $defaultCasts[$column['Field']];
            } else {
                $casts[$column['Field']] = $this->getCastType($column['Type']);
            }
            $phpType = $this->getPhpType($casts[$column['Field']]);

            $propertyName = $camelCase ? $this->camelize($column['Field']) : $column['Field'];
            $docBlock .= rtrim(sprintf(' * @property %s $%s %s', $phpType, $propertyName, $column['Comment'])) . "\n";
        }

        // 获取getXxxAttribute的定义
        $reflectionClass = new ReflectionClass(wei()->$model);
        preg_match_all('/(?<=^|;)get([^;]+?)Attribute(;|$)/', implode(';', get_class_methods(wei()->$model)), $matches);
        foreach ($matches[1] as $key => $attr) {
            $propertyName = $camelCase ? lcfirst($attr) : $this->snake($attr);
            $method = rtrim($matches[0][$key], ';');
            $reflectionMethod = $reflectionClass->getMethod($method);
            $name = $this->getDocCommentTitle($reflectionMethod->getDocComment());
            $return = $this->getMethodReturn($reflectionClass, $reflectionMethod);
            $docBlock .= rtrim(sprintf(' * @property %s $%s %s', $return, $propertyName, $name)) . "\n";
        }

        $class = ucfirst(substr($model, 0, -strlen('Record'))) . 'Trait';
        $file = $this->getFile($plugin, $class);
        $this->createFile($file, $this->getNamespace($plugin), $class, $docBlock, $casts);
    }

    protected function getCastType($type)
    {
        $type = explode('(', $type)[0];
        switch ($type) {
            case 'int':
            case 'tinyint':
                return 'int';

            case 'varchar':
            case 'char':
                return 'string';

            case 'timestamp':
            case 'datetime':
                return 'datetime';

            case 'date':
                return 'date';

            case 'text':
                return 'string';

            case 'decimal':
                return 'float';

            default:
                return $type;
        }
    }

    protected function getPhpType($type)
    {
        switch ($type) {
            case 'int':
            case 'tinyint':
                return 'int';

            case 'varchar':
            case 'char':
                return 'string';

            case 'timestamp':
            case 'datetime':
                return 'string';

            case 'date':
                return 'string';

            case 'text':
                return 'string';

            case 'json':
                return 'array';

            case 'decimal':
                return 'float';

            default:
                return $type;
        }
    }

    protected function getTable($class)
    {
        if (wei()->isEndsWith($class, 'Record')) {
            $class = substr($class, 0, -6);
        }

        $table = $this->snake($class);
        $table .= 's';

        return $table;
    }

    protected function snake($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    protected function getFile(BasePlugin $plugin, $name)
    {
        return $plugin->getBasePath() . '/src/Metadata/' . ucfirst($name) . '.php';
    }

    protected function createDir($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
    }

    protected function getNamespace(BasePlugin $plugin)
    {
        $class = get_class($plugin);
        $parts = explode('\\', $class);
        array_pop($parts);

        return implode('\\', $parts) . '\Metadata';
    }

    protected function createFile($file, $namespace, $class, $docBlock, $casts)
    {
        $table = $this->getTable($class);

        $this->writeln('生成文件 ' . $this->cli->success($file));

        ob_start();
        require $this->plugin->getById('plugin')->getBasePath() . '/resources/stubs/metadata.php';
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
     * Camelizes a word
     *
     * @param string $word The word to camelize
     *
     * @return string The camelized word
     */
    protected function camelize($word)
    {
        return lcfirst(str_replace(' ', '', ucwords(strtr($word, '_-', '  '))));
    }

    protected function getMethodReturn(ReflectionClass $class, ReflectionMethod $method)
    {
        $doc = $method->getDocComment();
        preg_match('/@return (.+?)\n/', $doc, $matches);
        if (!$matches) {
            return false;
        }

        return $matches[1] ?: false;
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

    /**
     * @param $class
     * @param bool $autoload
     * @return array
     * @link http://php.net/manual/en/function.class-uses.php#110752
     */
    public function classUsesDeep($class, $autoload = true)
    {
        $traits = [];
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));
        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }

        return array_unique($traits);
    }
}
