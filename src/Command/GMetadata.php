<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\BasePlugin;
use Miaoxing\Plugin\Model\CamelCaseTrait;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @mixin \StrMixin
 * @mixin \PluginMixin
 * @internal will change in the future
 */
final class GMetadata extends BaseCommand
{
    public function handle()
    {
        $id = $this->input->getArgument('plugin-id');

        $plugin = wei()->plugin->getOneById($id);

        $dir = $plugin->getBasePath() . '/src/Metadata/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $services = wei()->classMap->generate(
            [$plugin->getBasePath() . '/src'],
            '/Service/?*Model.php', // 排除 Model.php
            'Service',
            false
        );

        foreach ($services as $name => $class) {
            $uses = $this->classUsesDeep($class);
            $camelCase = isset($uses[CamelCaseTrait::class]);
            $this->createClass($name, $plugin, $camelCase);
        }

        return $this->suc('创建成功');
    }

    /**
     * @param string $class
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

    protected function configure()
    {
        $this
            ->addArgument('plugin-id', InputArgument::REQUIRED, 'The id of plugin');
    }

    protected function createClass($model, $plugin, $camelCase)
    {
        $modelObject = wei()->{$model}();
        $table = $modelObject->db->getTable($modelObject->getTable());
        $columns = wei()->db->fetchAll('SHOW FULL COLUMNS FROM ' . $table);

        $casts = [];
        $docBlock = '';
        foreach ($columns as $column) {
            $casts[$column['Field']] = $this->getCastType($column['Type']);
            $phpType = $this->getPhpType($casts[$column['Field']]);

            $propertyName = $camelCase ? $this->str->camel($column['Field']) : $column['Field'];
            $docBlock .= rtrim(sprintf(' * @property %s $%s %s', $phpType, $propertyName, $column['Comment'])) . "\n";
        }

        // 获取getXxxAttribute的定义
        $reflectionClass = new ReflectionClass($modelObject);
        preg_match_all('/(?<=^|;)get([^;]+?)Attribute(;|$)/', implode(';', get_class_methods($modelObject)), $matches);
        foreach ($matches[1] as $key => $attr) {
            $propertyName = $camelCase ? lcfirst($attr) : $this->str->snake($attr);
            if (isset($casts[$propertyName])) {
                continue;
            }

            $method = rtrim($matches[0][$key], ';');
            $reflectionMethod = $reflectionClass->getMethod($method);
            $name = $this->getDocCommentTitle($reflectionMethod->getDocComment());
            $return = $this->getMethodReturn($reflectionClass, $reflectionMethod) ?: 'mixed';
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
        $length = (int) ($parts[1] ?? 0);

        switch ($type) {
            case 'int':
            case 'smallint':
            case 'mediumint':
                return 'int';

            case 'tinyint':
                return 1 === $length ? 'bool' : 'int';

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
        if (is_array($type)) {
            $type = $type[0];
        }

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
            case 'list':
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

        $this->suc('生成文件 ' . $file);

        ob_start();
        require $this->plugin->getById('plugin')->getBasePath() . '/stubs/metadata.php';
        $content = ob_get_clean();

        file_put_contents($file, $content);
        chmod($file, 0777);
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
}
