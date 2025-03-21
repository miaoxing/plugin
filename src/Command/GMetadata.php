<?php

namespace Miaoxing\Plugin\Command;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlockFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Wei\BaseModel;
use Wei\Model\CamelCaseTrait;
use Wei\Model\Relation;
use Wei\ModelTrait;

/**
 * @mixin \StrPropMixin
 * @mixin \ClsPropMixin
 * @mixin \PluginPropMixin
 * @mixin \ClassMapPropMixin
 * @experimental will refactor to add more features
 */
final class GMetadata extends BaseCommand
{
    public function handle()
    {
        $pluginIds = [];
        $pluginId = $this->getArgument('plugin-id');
        if ($pluginId) {
            $pluginIds[] = $pluginId;
        } else {
            foreach ($this->plugin->getAll() as $plugin) {
                $pluginIds[] = $plugin->getId();
            }
        }

        foreach ($pluginIds as $pluginId) {
            $plugin = $this->plugin->getOneById($pluginId);
            $services = $this->classMap->generate(
                [$plugin->getBasePath() . '/src'],
                '/Service/?*Model.php', // 排除 Model.php
                'Service',
                false
            );
            foreach ($services as $class) {
                if (!is_subclass_of($class, BaseModel::class)) {
                    continue;
                }
                $this->updateClass($class);
            }
        }

        $this->suc('创建成功');
    }

    protected function configure()
    {
        $this
            ->addArgument('plugin-id', InputArgument::OPTIONAL, 'The id of plugin')
            ->addOption('rewrite', 'r', InputOption::VALUE_NONE, 'Whether to rewrite the existing metadata');
    }

    protected function updateClass(string $modelClass)
    {
        /** @var BaseModel $model */
        $model = new $modelClass();
        $reflectionClass = new \ReflectionClass($model);
        $camelCase = $this->cls->usesDeep($modelClass)[CamelCaseTrait::class] ?? false;

        // 生成表格字段的属性的注释
        $docBlocks = $this->getDocBlocksFromTable($model, $camelCase);

        // 生成 getXxxAttribute 的方法定义的属性的注释
        $docBlocks = array_merge($docBlocks, $this->getDocBlocksFromAccessors($reflectionClass, $camelCase));

        // 获取关联的定义
        $docBlocks = array_merge($docBlocks, $this->getDocBlocksFromRelationMethods($reflectionClass));

        $docComment = $reflectionClass->getDocComment();
        $factory = DocBlockFactory::createInstance();
        if ($docComment) {
            $docblock = $factory->create($docComment);
        } else {
            $docblock = new DocBlock();
        }

        $properties = [];
        foreach ($docblock->getTags() as $tag) {
            if (!$tag instanceof Property) {
                continue;
            }
            $properties[$tag->getVariableName()] = $tag;
        }

        // 没有的新增
        $new = [];
        foreach ($docBlocks as $propertyName => $docBlock) {
            if (!isset($properties[$propertyName])) {
                $new[$propertyName] = $docBlock;
            }
        }
        $docComment = $this->addDocComment($docComment, implode("\n", $new));

        // 已有的重写
        if ($this->getOption('rewrite')) {
            foreach ($docblock->getTags() as $tag) {
                if (!$tag instanceof Property) {
                    continue;
                }
                if (isset($docBlocks[$tag->getVariableName()])) {
                    $docComment = str_replace(' * @property ' . $tag, $docBlocks[$tag->getVariableName()], $docComment);
                }
            }
        }

        // 写入文件
        $this->updateDocComment($reflectionClass, $docComment);
        $this->suc('更新文件 ' . $reflectionClass->getFileName());
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

    /**
     * 生成表格字段的属性的注释
     *
     * @param BaseModel $modelObject
     * @param bool $camelCase
     * @return array
     */
    protected function getDocBlocksFromTable(BaseModel $modelObject, bool $camelCase): array
    {
        $table = $modelObject->getDb()->getTable($modelObject->getTable());
        $columns = wei()->db->fetchAll('SHOW FULL COLUMNS FROM ' . $table);
        $modelColumns = $modelObject->getColumns();

        $docBlocks = [];
        foreach ($columns as $column) {
            $propertyName = $camelCase ? $this->str->camel($column['Field']) : $column['Field'];
            $cast = $modelColumns[$propertyName]['cast'] ?? null;

            if ('list' === $cast || 'list' === ($cast[0] ?? null)) {
                $phpType = 'array';
            } elseif ('object' === $cast) {
                $phpType = 'object';
            } else {
                $phpType = $this->getPhpType($column['Type']);
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

        return $docBlocks;
    }

    /**
     * 生成 getXxxAttribute 的方法定义的属性的注释
     *
     * @param \ReflectionClass $reflectionClass
     * @param bool $camelCase
     * @return array
     */
    protected function getDocBlocksFromAccessors(\ReflectionClass $reflectionClass, bool $camelCase): array
    {
        $docBlocks = [];
        preg_match_all(
            '/(?<=^|;)get([^;]+?)Attribute(;|$)/',
            implode(';', get_class_methods($reflectionClass->getName())),
            $matches
        );
        foreach ($matches[1] as $key => $attr) {
            $propertyName = $camelCase ? lcfirst($attr) : $this->str->snake($attr);
            if (isset($docBlocks[$propertyName])) {
                continue;
            }

            $method = rtrim($matches[0][$key], ';');
            $reflectionMethod = $reflectionClass->getMethod($method);
            $name = $this->getDocCommentTitle($reflectionMethod->getDocComment());
            $return = $this->getMethodReturn($reflectionMethod);
            $docBlocks[$propertyName] = rtrim(sprintf(' * @property %s $%s %s', $return, $propertyName, $name));
        }
        return $docBlocks;
    }

    /**
     * 生成关联方法的注释
     *
     * @param \ReflectionClass $reflectionClass
     * @return array
     */
    protected function getDocBlocksFromRelationMethods(\ReflectionClass $reflectionClass): array
    {
        $properties = [];
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($this->isRelation($method, $reflectionClass)) {
                $propertyName = $method->getName();
                $returnName = $this->getMethodReturn($method);
                $properties[$propertyName] = rtrim(sprintf(
                    ' * @property %s $%s %s',
                    $returnName,
                    $propertyName,
                    $this->getDocCommentTitle($method->getDocComment())
                ));
            }
        }
        return $properties;
    }

    protected function isRelation(\ReflectionMethod $method, \ReflectionClass $reflectionClass): bool
    {
        // PHP 8
        if (method_exists($method, 'getAttributes') && $method->getAttributes(Relation::class)) {
            return true;
        }

        // Compat with PHP less than 8
        if (false !== strpos($method->getDocComment() ?: '', '@Relation')) {
            return true;
        }

        $returnType = $method->getReturnType();
        if (!$returnType) {
            return false;
        }

        if ($returnType instanceof \ReflectionNamedType && !is_subclass_of($returnType->getName(), BaseModel::class)) {
            return false;
        }

        // 跳过 ModelTrait 和父类方法
        if (method_exists(ModelTrait::class, $method->getName())) {
            return false;
        }
        if ($method->getDeclaringClass()->getName() !== $reflectionClass->getName()) {
            return false;
        }

        return true;
    }

    /**
     * 获取方法的返回类型，优先从注释获取，其次是方法的返回类型
     *
     * @param \ReflectionMethod $method
     * @return string
     */
    protected function getMethodReturn(\ReflectionMethod $method): string
    {
        $docComment = $method->getDocComment();
        if ($docComment) {
            // 不使用 PHPDoc 解析，因为类名默认会加上全局命名空间
            preg_match('/@return (.+?)\s/', $docComment, $matches);
            if ($matches) {
                return $matches[1];
            }
        }

        $returnType = $method->getReturnType();
        if (!$returnType) {
            return 'mixed';
        }

        // 使用静态解析，不使用反射，因为返回值会包含命名空间，实际命名空间已经导入了
        $startLine = $method->getStartLine();
        $endLine = $method->getEndLine();
        $source = file($method->getFileName());
        $methodCode = implode('', array_slice($source, $startLine - 1, $endLine - $startLine + 1));
        preg_match('/\)\s*:\s*(.+?)\s/', $methodCode, $matches);
        if ($matches) {
            return $matches[1];
        }
        return 'mixed';
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

    /**
     * 往类注释插入新的内容
     *
     * 内容需以 `* ` 开头
     *
     * @param string $docComment
     * @param string $newDocBlock
     * @return string
     */
    protected function addDocComment(string $docComment, string $newDocBlock): string
    {
        if (!$newDocBlock) {
            return $docComment;
        }

        $lines = explode("\n", $docComment);

        if (count($lines) > 1) {
            array_splice($lines, -1, 0, $newDocBlock);
        } else {
            $lines = [
                '/**',
                $newDocBlock,
                ' */',
            ];
        }

        // 将数组重新组合成字符串并返回
        return implode("\n", $lines);
    }

    /**
     * 更新类注释为新的内容
     *
     * @param \ReflectionClass $reflectionClass
     * @param string $newDocComment
     * @return void
     */
    protected function updateDocComment(\ReflectionClass $reflectionClass, string $newDocComment)
    {
        $file = $reflectionClass->getFileName();
        $fileContent = file_get_contents($file);

        $docComment = $reflectionClass->getDocComment();
        if (!$docComment) {
            $startLine = $reflectionClass->getStartLine();
            $lines = explode(\PHP_EOL, $fileContent);

            array_splice($lines, $startLine - 1, 0, [$newDocComment]);
            $fileContent = implode(\PHP_EOL, $lines);
        } else {
            $fileContent = str_replace($docComment, $newDocComment, $fileContent);
        }

        file_put_contents($file, $fileContent);
    }
}
