<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\Model\ModelTrait;
use Wei\Base;

/**
 * @mixin \EnvMixin
 * @mixin \CacheMixin
 * @mixin \AppMixin
 * @mixin \PhpFileCacheMixin
 */
class Config extends \Wei\Config
{
    /**
     * @internal
     */
    protected const TYPE_STRING = 's';

    /**
     * @internal
     */
    protected const TYPE_BOOL = 'b';

    /**
     * @internal
     */
    protected const TYPE_INT = 'i';

    /**
     * @internal
     */
    protected const TYPE_FLOAT = 'f';

    /**
     * @internal
     */
    protected const TYPE_NULL = 'n';

    /**
     * @internal
     */
    protected const TYPE_ARRAY = 'a';

    /**
     * @internal
     */
    protected const TYPE_OBJECT = 'o';

    /**
     * @internal
     */
    protected const TYPE_JSON = 'j';

    /**
     * @internal
     */
    protected const TYPE_EXPRESS = 'e';

    /**
     * The local config file path
     *
     * @var string
     */
    protected $localFile = 'storage/configs/%env%.php';

    /**
     * @var array
     */
    protected $services = [];

    /**
     * The missing config names of the last get action
     *
     * @var array<string>
     */
    protected $missing = [];

    /**
     * Native scalar type to config type
     *
     * @var string[]
     * @internal
     */
    protected $scalarTypes = [
        'boolean' => self::TYPE_BOOL,
        'integer' => self::TYPE_INT,
        'double' => self::TYPE_FLOAT,
        'string' => self::TYPE_STRING,
        'NULL' => self::TYPE_NULL,
        // ignore non-scalar types
    ];

    /**
     * @svc
     * @param mixed $default
     */
    protected function get(string $name, $default = null)
    {
        return $this->getMultiple([$name], [$name => $default])[$name];
    }

    /**
     * @svc
     * @param mixed $value
     */
    protected function set(string $name, $value, array $options = []): self
    {
        return $this->setApp($name, $value, $options);
    }

    /**
     * @svc
     */
    protected function getMultiple(array $names, array $defaults = []): array
    {
        $values = $this->getAppMultiple($names);

        if ($this->missing) {
            $globalValues = $this->getGlobalMultiple($this->missing, $defaults);
            $values = array_merge($values, $globalValues);
        }

        foreach ($this->missing as $name) {
            // 注意: 配置名称不含 . 时，文件配置会返回所有下级数据
            // 行为和数据库存储配置不一致，但一般不影响使用
            $values[$name] = $this->wei->getConfig($name, $defaults[$name] ?? null);
        }

        return $values;
    }

    /**
     * @svc
     */
    protected function setMultiple(array $values, array $options = []): self
    {
        return $this->setAppMultiple($values, $options);
    }

    /**
     * @svc
     */
    protected function getSection(string $name): array
    {
        return array_merge(
            (array) $this->wei->getConfig($name),
            $this->getGlobalSection($name),
            $this->getAppSection($name)
        );
    }

    /**
     * @svc
     * @param mixed $default
     */
    protected function getGlobal(string $name, $default = null)
    {
        return $this->getMultipleBy(GlobalConfigModel::class, [$name], [$name => $default])[$name];
    }

    /**
     * @svc
     * @param mixed $value
     */
    protected function setGlobal(string $name, $value, array $options = []): self
    {
        return $this->setMultipleBy(GlobalConfigModel::class, [$name => $value], $options);
    }

    /**
     * @svc
     */
    protected function deleteGlobal(string $name): self
    {
        return $this->deleteBy(GlobalConfigModel::class, $name);
    }

    /**
     * @svc
     */
    protected function getGlobalMultiple(array $names, array $defaults = []): array
    {
        return $this->getMultipleBy(GlobalConfigModel::class, $names, $defaults);
    }

    /**
     * @svc
     */
    protected function setGlobalMultiple(array $values, array $options = []): self
    {
        return $this->setMultipleBy(GlobalConfigModel::class, $values, $options);
    }

    /**
     * @svc
     */
    protected function getGlobalSection(string $name): array
    {
        return $this->getSectionBy(GlobalConfigModel::class, $name);
    }

    /**
     * @svc
     * @param mixed $default
     */
    protected function getApp(string $name, $default = null)
    {
        return $this->getMultipleBy(ConfigModel::class, [$name], [$name => $default])[$name];
    }

    /**
     * @svc
     * @param mixed $value
     */
    protected function setApp(string $name, $value, array $options = []): self
    {
        return $this->setMultipleBy(ConfigModel::class, [$name => $value], $options);
    }

    /**
     * @svc
     */
    protected function deleteApp(string $name): self
    {
        return $this->deleteBy(ConfigModel::class, $name);
    }

    /**
     * @svc
     */
    protected function getAppMultiple(array $names, array $defaults = []): array
    {
        return $this->getMultipleBy(ConfigModel::class, $names, $defaults);
    }

    /**
     * @svc
     */
    protected function setAppMultiple(array $values, array $options = []): self
    {
        return $this->setMultipleBy(ConfigModel::class, $values, $options);
    }

    /**
     * @svc
     */
    protected function getAppSection(string $name): array
    {
        return $this->getSectionBy(ConfigModel::class, $name);
    }

    /**
     * @template T
     * @param string|class-string<T> $name
     * @return T|Base
     * @svc
     */
    protected function createService(string $name): Base
    {
        $name = $this->wei->getServiceName($name);
        $options = $this->getSection($name);
        return $this->wei->newInstance($name, $options);
    }

    /**
     * @template T
     * @param string|class-string<T> $name
     * @return T|Base
     * @svc
     */
    protected function getService(string $name): Base
    {
        $appId = $this->app->getId();
        $name = $this->wei->getServiceName($name);
        if (!isset($this->services[$appId][$name])) {
            $this->services[$appId][$name] = $this->createService($name);
        }
        return $this->services[$appId][$name];
    }

    /**
     * 预加载全局配置
     *
     * @experimental
     * @svc
     */
    protected function preloadGlobal()
    {
        // 1. 先获取本地配置
        $configs = $this->getPhpFileCache('global-config', []);

        // 2. 检查更新配置
        if ($this->needsUpdatePreload($configs)) {
            $configs = $this->setPreloadCache();
        }

        // 3. 加载配置
        $this->wei->setConfig($configs);
    }

    /**
     * @return $this
     * @svc
     */
    protected function publishPreload(): self
    {
        $this->updatePreloadVersion();
        $configs = $this->setPreloadCache();
        $this->wei->setConfig($configs);
        return $this;
    }

    /**
     * @return string
     * @internal
     */
    protected function updatePreloadVersion(): string
    {
        $version = date('Y-m-d H:i:s');
        $this->setGlobal($this->getPreloadVersionKey(), $version, ['preload' => true]);
        return $version;
    }

    /**
     * @param string|class-string<ModelTrait> $model
     * @param array $names
     * @param array $defaults
     * @return array
     */
    protected function getMultipleBy(string $model, array $names, array $defaults): array
    {
        $this->missing = [];
        $prefix = $this->getPrefix($model);

        // From cache
        if (count($names) === 1) {
            $key = current($names);
            $values = [$key => $this->cache->get($prefix . $key)];
        } else {
            $values = $this->cacheWithPrefix($prefix, function () use ($names) {
                return $this->cache->getMultiple($names);
            });
        }

        $missing = [];
        foreach ($values as $name => $value) {
            if (!$this->cache->isHit($name)) {
                $missing[] = $name;
                $values[$name] = $defaults[$name] ?? null;
            }
        }

        // From database
        if ($missing) {
            $dbConfigs = $model::select(['name', 'type', 'value'])
                ->where('name', $missing)
                ->fetchAll();
            if (!$dbConfigs) {
                // TODO 要计入缓存
                $this->missing = $missing;
                return $values;
            }

            $configs = [];
            foreach ($dbConfigs as $config) {
                $configs[$config['name']] = $this->decode($config['value'], $config['type']);
            }

            // Next time will fetch from cache
            $this->cacheWithPrefix($prefix, function () use ($configs) {
                $this->cache->setMultiple($configs);
            });

            $this->missing = array_diff($missing, array_keys($configs));
            $values = array_merge($values, $configs);
        }

        return $values;
    }

    /**
     * @param string|class-string<ModelTrait> $model
     * @param array $values
     * @param array $options
     * @return $this
     */
    protected function setMultipleBy(string $model, array $values, array $options = []): self
    {
        if (!$values) {
            return $this;
        }

        $configs = $model::where('name', array_keys($values))
            ->indexBy('name')
            ->all();

        foreach ($values as $name => $value) {
            if (!isset($configs[$name])) {
                $configs[$name] = $model::fromArray(['name' => $name]);
            }

            [$value, $type] = $this->encode($value);
            $configs[$name]->fromArray([
                'type' => $type,
                'value' => $value,
            ]);

            // For global config
            if (isset($options['preload'])) {
                $configs[$name]->preload = $options['preload'];
            }
        }

        $configs->save();

        $data = [];
        $prefix = $this->getPrefix($model);
        foreach ($values as $key => $value) {
            $data[$prefix . $key] = $value;
        }
        $this->cache->setMultiple($data);

        return $this;
    }

    /**
     * @param string|class-string<ModelTrait> $model
     * @param string $name
     * @return $this
     * @internal
     */
    protected function deleteBy(string $model, string $name): self
    {
        $this->cache->delete($this->getPrefix($model) . $name);

        $config = $model::findBy('name', $name);
        $config && $config->destroy();

        return $this;
    }

    /**
     * @param string|class-string<ModelTrait> $model
     * @param string $name
     * @return array
     */
    protected function getSectionBy(string $model, string $name): array
    {
        // From database
        $configs = $model::select(['name', 'type', 'value'])
            ->where('name', 'like', $name . '.%')
            ->fetchAll();

        $length = strlen($name) + 1;
        $values = [];
        foreach ($configs as $config) {
            $values[substr($config['name'], $length)] = $this->decode($config['value'], $config['type']);
        }

        return $values;
    }

    /**
     * @param string $model
     * @return string
     */
    protected function getPrefix(string $model): string
    {
        if (ConfigModel::class === $model) {
            return 'config:' . $this->app->getId() . ':';
        }
        return 'globalConfig:';
    }

    /**
     * @param string $prefix
     * @param callable $fn
     * @return mixed
     * @internal
     */
    protected function cacheWithPrefix(string $prefix, callable $fn)
    {
        $namespace = $this->cache->getNamespace();
        $this->cache->setNamespace($namespace . $prefix);
        $result = $fn();
        $this->cache->setNamespace($namespace);
        return $result;
    }

    /**
     * Convert PHP value to config value
     *
     * @param mixed $value
     * @return array
     * @internal
     */
    protected function encode($value): array
    {
        $type = gettype($value);
        if (isset($this->scalarTypes[$type])) {
            return [$value, $this->scalarTypes[$type]];
        }

        if ('array' === $type) {
            return [json_encode($value, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE), static::TYPE_ARRAY];
        }

        if ($value instanceof \stdClass) {
            return [json_encode($value, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE), static::TYPE_JSON];
        }

        return [serialize($value), static::TYPE_OBJECT];
    }

    /**
     * Convert config value to PHP value
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     * @internal
     */
    protected function decode($value, string $type)
    {
        switch ($type) {
            case static::TYPE_STRING:
                return (string) $value;

            case static::TYPE_INT:
                return (int) $value;

            case static::TYPE_FLOAT:
                return (float) $value;

            case static::TYPE_BOOL:
                return filter_var($value, \FILTER_VALIDATE_BOOLEAN);

            case static::TYPE_ARRAY:
                return json_decode($value, true);

            case static::TYPE_JSON:
                return json_decode($value);

            case static::TYPE_OBJECT:
                return unserialize($value);

            case static::TYPE_NULL:
                return null;

            default:
                return $value;
        }
    }

    /**
     * 获取配置文件
     *
     * @return string
     */
    protected function getLocalFile(): string
    {
        return str_replace('%env%', $this->env->getName(), $this->localFile);
    }

    /**
     * 更新配置到本地文件中
     *
     * @param array $configs
     * @svc
     */
    protected function updateLocal(array $configs)
    {
        $file = $this->getLocalFile();
        if (is_file($file)) {
            $localConfigs = require $file;
        } else {
            $localConfigs = [];
        }

        foreach ($configs as $name => $value) {
            if (isset($localConfigs[$name])) {
                $localConfigs[$name] = array_merge($localConfigs[$name], $value);
            } else {
                $localConfigs[$name] = $value;
            }
        }

        $this->writeConfig($file, $localConfigs);
        $this->wei->setConfig($configs);
    }

    /**
     * @param string $file
     * @param mixed $configs
     */
    protected function writeConfig(string $file, $configs): void
    {
        $content = $this->generateContent($configs);
        file_put_contents($file, $content);
        function_exists('opcache_invalidate') && opcache_invalidate($file);
    }

    /**
     * 将数据库读出的对象生成文件内容
     *
     * @param array $configs
     * @return string
     */
    protected function generateContent($configs)
    {
        return "<?php\n\nreturn " . $this->varExport($configs) . ";\n";
    }

    /**
     * @param mixed $var
     * @param string $indent
     * @return string
     * @link https://stackoverflow.com/questions/24316347/how-to-format-var-export-to-php5-4-array-syntax
     */
    protected function varExport($var, $indent = '')
    {
        switch (gettype($var)) {
            case 'string':
                return '\'' . addcslashes($var, "\\\$\\'\r\n\t\v\f") . '\'';
            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $result = [];
                foreach ($var as $key => $value) {
                    $result[] = $indent . '    '
                        . ($indexed ? '' : $this->varExport($key) . ' => ')
                        . $this->varExport($value, "$indent    ");
                }

                return "[\n" . implode(",\n", $result) . ($result ? ',' : '') . "\n" . $indent . ']';
            case 'boolean':
                return $var ? 'true' : 'false';

            case 'NULL':
                return 'null';

            case 'object':
                if (isset($var->express)) {
                    return $var->express;
                }
            // no break

            default:
                return var_export($var, true);
        }
    }

    /**
     * 判断本地的配置是否需要更改
     *
     * @param array $configs
     * @return bool
     * @internal
     */
    protected function needsUpdatePreload(array $configs): bool
    {
        $key = $this->getPreloadVersionKey();

        $version = $this->getGlobal($key);
        if (!$version) {
            $version = $this->updatePreloadVersion();
        }

        [$service, $option] = explode('.', $key);
        return !isset($configs[$service][$option]) || $configs[$service][$option] < $version;
    }

    /**
     * @return string
     * @svc
     */
    protected function getPreloadVersionKey(): string
    {
        return 'config.preloadVersion';
    }

    /**
     * @return array
     * @internal
     */
    protected function setPreloadCache(): array
    {
        $configs = GlobalConfigModel::select('name', 'type', 'value')
            ->where('preload', true)
            ->fetchAll();

        $data = [];
        foreach ($configs as $config) {
            // 从右边的点(.)拆分为两部分,兼容a.b.c的等情况
            $pos = strrpos($config['name'], '.');
            $service = substr($config['name'], 0, $pos);
            $option = substr($config['name'], $pos + 1);
            $data[$service][$option] = $this->decode($config['value'], $config['type']);
        }

        $this->setPhpFileCache('global-config', $data);

        return $data;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @internal
     */
    protected function setPhpFileCache(string $key, $value)
    {
        $file = $this->phpFileCache->getDir() . '/' . $key . '.php';
        $this->writeConfig($file, $value);
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     * @internal
     */
    protected function getPhpFileCache(string $key, $default = null)
    {
        $file = $this->phpFileCache->getDir() . '/' . $key . '.php';
        if (is_file($file)) {
            return require $file;
        }
        return $default;
    }
}
