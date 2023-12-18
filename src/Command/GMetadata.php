<?php

namespace Miaoxing\Plugin\Command;

use Miaoxing\Plugin\BasePlugin;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Console\Input\InputArgument;
use Wei\BaseModel;
use Wei\Model\CamelCaseTrait;

/**
 * @mixin \StrMixin
 * @mixin \ClsMixin
 * @mixin \PluginMixin
 * @internal will change in the future
 */
final class GMetadata extends BaseCommand
{
    use PluginIdTrait;

    public function handle()
    {
        $id = $this->getPluginId();

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
            $uses = $this->cls->usesDeep($class);
            $camelCase = isset($uses[CamelCaseTrait::class]);
            $this->createClass($name, $plugin, $camelCase);
        }

        $this->suc('创建成功');
    }

    protected function configure()
    {
        $this
            ->addArgument('plugin-id', InputArgument::OPTIONAL, 'The id of plugin');
    }

    protected function createClass($model, $plugin, $camelCase)
    {
        /** @var BaseModel $modelObject */
        $modelObject = $this->wei->get($model);
        $table = $modelObject->getDb()->getTable($modelObject->getTable());
        $columns = wei()->db->fetchAll('SHOW FULL COLUMNS FROM ' . $table);
        $modelColumns = $modelObject->getColumns();

        $docBlocks = [];
        foreach ($columns as $column) {
            $propertyName = $camelCase ? $this->str->camel($column['Field']) : $column['Field'];

            $phpType = $this->getPhpType($column['Type']);

            // TODO 支持其他类型
            if ('json' === $column['Type'] && ($modelColumns[$propertyName]['cast'] ?? null) === 'object') {
                $phpType = 'object';
            }

            if (isset($modelColumns[$propertyName]['nullable']) && $modelColumns[$propertyName]['nullable']) {
                $phpType .= '|null';
            }

            $propertyName = $camelCase ? $this->str->camel($column['Field']) : $column['Field'];
            $docBlocks[$propertyName] = rtrim(sprintf(
                ' * @property %s $%s %s',
                $phpType,
                $propertyName,
                $column['Comment']
            ));
        }

        // 获取getXxxAttribute的定义
        $reflectionClass = new ReflectionClass($modelObject);
        preg_match_all('/(?<=^|;)get([^;]+?)Attribute(;|$)/', implode(';', get_class_methods($modelObject)), $matches);
        foreach ($matches[1] as $key => $attr) {
            $propertyName = $camelCase ? lcfirst($attr) : $this->str->snake($attr);
            if (isset($docBlocks[$propertyName])) {
                continue;
            }

            $method = rtrim($matches[0][$key], ';');
            $reflectionMethod = $reflectionClass->getMethod($method);
            $name = $this->getDocCommentTitle($reflectionMethod->getDocComment());
            $return = $this->getMethodReturn($reflectionClass, $reflectionMethod) ?: 'mixed';
            $docBlocks[$propertyName] = rtrim(sprintf(' * @property %s $%s %s', $return, $propertyName, $name));
        }

        $class = ucfirst(substr($model, 0, -strlen('Model'))) . 'Trait';
        $file = $this->getFile($plugin, $class);
        $docBlock = implode("\n", $docBlocks) . "\n";
        $this->createFile($file, $this->getNamespace($plugin), $class, $docBlock);
    }

    protected function getPhpType($columnType)
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

            case 'bigint':
            case 'varchar':
            case 'char':
            case 'mediumtext':
            case 'text':
            case 'timestamp':
            case 'datetime':
            case 'date':
            case 'decimal':
            case 'binary':
            case 'varbinary':
                return 'string';

            case 'json':
                return 'array';

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

    protected function createFile($file, $namespace, $class, $docBlock)
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
