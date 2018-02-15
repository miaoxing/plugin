<?php

namespace Miaoxing\Plugin\Controller\Cli;

use Miaoxing\Plugin\BaseController;
use Miaoxing\Plugin\BasePlugin;
use Miaoxing\Plugin\CliDefinitionTrait;
use Miaoxing\Plugin\Service\Str;
use ReflectionClass;
use ReflectionMethod;

/**
 * @property Str $str
 */
class Metadata extends BaseController
{
    use CliDefinitionTrait;

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

        $dir = $plugin->getBasePath() . '/src/Metadata/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $services = $this->plugin->generateClassMap(
            [$plugin->getBasePath() . '/src'],
            '/Service/*Model.php',
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
        $modelObject = wei()->$model();
        $table = $modelObject->getTable();
        $defaultCasts = $modelObject->getOption('defaultCasts') ?: [];
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

            $propertyName = $camelCase ? $this->str->camel($column['Field']) : $column['Field'];
            $docBlock .= rtrim(sprintf(' * @property %s $%s %s', $phpType, $propertyName, $column['Comment'])) . "\n";
        }

        // 获取getXxxAttribute的定义
        $reflectionClass = new ReflectionClass($modelObject);
        preg_match_all('/(?<=^|;)get([^;]+?)Attribute(;|$)/', implode(';', get_class_methods($modelObject)), $matches);
        foreach ($matches[1] as $key => $attr) {
            $propertyName = $camelCase ? lcfirst($attr) : $this->str->snake($attr);
            $method = rtrim($matches[0][$key], ';');
            $reflectionMethod = $reflectionClass->getMethod($method);
            $name = $this->getDocCommentTitle($reflectionMethod->getDocComment());
            $return = $this->getMethodReturn($reflectionClass, $reflectionMethod);
            $docBlock .= rtrim(sprintf(' * @property %s $%s %s', $return, $propertyName, $name)) . "\n";
        }

        $class = ucfirst(substr($model, 0, -strlen('Model'))) . 'Trait';
        $file = $this->getFile($plugin, $class);
        $this->createFile($file, $this->getNamespace($plugin), $class, $docBlock, $casts);
    }

    protected function getCastType($columnType)
    {
        $parts = explode('(', $columnType);
        $type = $parts[0];
        $length = (int) $parts[1];

        switch ($type) {
            case 'int':
                return 'int';

            case 'tinyint':
                return $length === 1 ? 'bool' : 'int';

            case 'varchar':
            case 'char':
            case 'mediumtext':
            case 'text':
                return 'string';

            case 'timestamp':
            case 'datetime':
                return 'datetime';

            case 'date':
                return 'date';

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

        $table = $this->str->snake($class);
        $table = $this->str->pluralize($table);

        return $table;
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
     * @see http://php.net/manual/en/function.class-uses.php#110752
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
