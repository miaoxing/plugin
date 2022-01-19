<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;

/**
 * @mixin \EnvMixin
 * @mixin \CacheMixin
 */
class Config extends \Wei\Config
{
    /**
     * @var array
     */
    protected $configs = [];

    /**
     * 配置文件的路径
     *
     * @var string
     */
    protected $configFile = 'storage/configs/%env%.php';

    /**
     * @var string
     */
    protected $cacheKeyPrefix = 'config:';

    /**
     * @var string
     */
    protected $globalCacheKeyPrefix = 'globalConfig:';

    protected $nativeTypes = [
        'boolean' => ConfigModel::TYPE_BOOL,
        'integer' => ConfigModel::TYPE_INT,
        'double' => ConfigModel::TYPE_FLOAT,
        'string' => ConfigModel::TYPE_STRING,
        'NULL' => ConfigModel::TYPE_NULL,
        //'array' => ConfigModel::TYPE_ARRAY,
        //'object' => ConfigModel::TYPE_JSON, // object?
        // unknown type
        // resource (closed)
    ];

    public function __construct($options = [])
    {
        parent::__construct($options);

        $file = $this->getConfigFile();
        if (is_file($file)) {
            $this->configs = require $this->getConfigFile();
        }
    }

    /**
     * 获取配置文件
     *
     * @return string
     */
    public function getConfigFile()
    {
        return str_replace('%env%', $this->env->getName(), $this->configFile);
    }

    /**
     * @svc
     */
    protected function get($name, $default = null)
    {
        $value = $this->getApp($name, $default);
        if ($value !== $default) {
            return $value;
        }

        $value = $this->getGlobal($name, $default);
        if ($value !== $default) {
            return $value;
        }

        // TODO 行为不一致
        // From file
        return $this->wei->getConfig($name, $default);
    }

    /**
     * @svc
     */
    protected function set($name, $value, array $options = []): self
    {
        return $this->setApp($name, $value, $options);
    }

    /**
     * @svc
     */
    protected function getMultiple(array $names, $default = null): array
    {
        $values = $this->getAppMultiple($names, $default);

        $missing = [];
        foreach ($values as $name => $value) {
            if ($value === $default) {
                $missing[] = $name;
            }
        }

        if ($missing) {
            $globalValues = $this->getGlobalMultiple($missing, $default);
            $values = array_merge($values, $globalValues);
        }

        return $values;
    }

    /**
     * @svc
     */
    protected function setMultiple(array $values, $options = []): self
    {
        return $this->setAppMultiple($values, $options);
    }

    /**
     * @svc
     */
    protected function getSection(string $name): array
    {
        return array_merge($this->getGlobalSection($name), $this->getAppSection($name));
    }

    /**
     * @svc
     */
    protected function getGlobal($name, $default = null)
    {
        // From cache
        $key = $this->globalCacheKeyPrefix . $name;
        $value = $this->cache->get($key);
        if (null !== $value) {
            return $value;
        }

        $config = GlobalConfigModel::select(['type', 'value'])->where('name', $name)->fetch();
        if ($config) {
            $value = $this->decode($config);
            $this->cache->set($key, $value);
            return $value;
        }

        return $default;
    }

    /**
     * @svc
     */
    protected function setGlobal($name, $value): self
    {
        [$type, $dbValue] = $this->encode($value);
        GlobalConfigModel::findOrInitBy(['name' => $name])->save([
            'type' => $type,
            'value' => $dbValue,
        ]);
        $this->cache->set($this->globalCacheKeyPrefix . $name, $value);
        return $this;
    }

    /**
     * @svc
     */
    protected function deleteGlobal(string $name): self
    {
        $this->cache->delete($this->globalCacheKeyPrefix . $name);

        $config = GlobalConfigModel::findBy('name', $name);
        $config && $config->destroy();

        return $this;
    }

    /**
     * @svc
     */
    protected function getGlobalMultiple($names, $default = null): array
    {
        // From cache
        $keys = [];
        foreach ($names as $name) {
            $keys[] = $this->globalCacheKeyPrefix . $name;
        }
        $caches = $this->cache->getMultiple($keys, $default);

        $values = [];
        $missing = [];
        foreach ($caches as $name => $value) {
            $name = substr($name, strlen($this->globalCacheKeyPrefix));
            if ($value !== $default) {
                $values[$name] = $value;
            } else {
                $missing[] = $name;
            }
        }

        // From database
        if ($missing) {
            $configs = GlobalConfigModel::select(['name', 'type', 'value'])
                ->where('name', $names)
                ->indexBy('name')
                ->fetchAll();
            $dbValues = [];
            foreach ($configs as $config) {
                $dbValues[$config['name']] = $this->decode($config);
            }
            $values = array_merge($values, $dbValues);
        }

        return $values;
    }

    /**
     * @svc
     */
    protected function setGlobalMultiple($values): self
    {
        if (!$values) {
            return $this;
        }

        $configs = GlobalConfigModel
            ::where('name', array_keys($values))
            ->indexBy('name')
            ->all();

        foreach ($values as $name => $value) {
            if (!isset($configs[$name])) {
                $configs[$name] = GlobalConfigModel::fromArray(['name' => $name]);
            }

            [$type, $value] = $this->encode($value);
            $configs[$name]->fromArray([
                'type' => $type,
                'value' => $value,
            ]);
        }

        $configs->save();

        $data = [];
        foreach ($values as $key => $value) {
            $data[$this->globalCacheKeyPrefix . $key] = $value;
        }
        $this->cache->setMultiple($data);

        return $this;
    }

    /**
     * @svc
     */
    protected function getGlobalSection($name): array
    {
        // From database
        $configs = GlobalConfigModel::select(['name', 'type', 'value'])
            ->where('name', 'like', $name . '.%')
            ->indexBy('name')
            ->fetchAll();

        $values = [];
        foreach ($configs as $config) {
            $values[substr($config['name'], strlen($name) + 1)] = $this->decode($config);
        }

        return $values;
    }

    /**
     * @svc
     */
    protected function getApp($name, $default = null)
    {
        // From cache
        $value = $this->getCacheItem($name);
        if (null !== $value) {
            return $value;
        }

        // From database
        $config = ConfigModel::select(['type', 'value'])->where('name', $name)->fetch();
        if ($config) {
            $value = $this->decode($config);
            $this->setCacheItem($name, $value);
            return $value;
        }

        return $default;
    }

    /**
     * @svc
     */
    protected function setApp($name, $value, array $options = []): self
    {
        [$type, $dbValue] = $this->encode($value);
        ConfigModel::findOrInitBy(['name' => $name])->save([
            'type' => $type,
            'value' => $dbValue,
        ]);
        $this->setCacheItem($name, $value);
        return $this;
    }

    /**
     * @svc
     */
    protected function deleteApp(string $name): self
    {
        $this->cache->delete($this->cacheKeyPrefix . $name);

        $config = ConfigModel::findBy('name', $name);
        $config && $config->destroy();

        return $this;
    }

    /**
     * @svc
     */
    protected function getAppMultiple($names, $default = null): array
    {
        // From cache
        $keys = [];
        foreach ($names as $name) {
            $keys[] = $this->cacheKeyPrefix . $name;
        }
        $caches = $this->cache->getMultiple($keys, $default);

        $values = [];
        $missing = [];
        foreach ($caches as $name => $value) {
            $name = substr($name, strlen($this->cacheKeyPrefix));
            if ($value !== $default) {
                $values[$name] = $value;
            } else {
                $missing[] = $name;
            }
        }

        // From database
        if ($missing) {
            $configs = ConfigModel::select(['name', 'type', 'value'])
                ->where('name', $names)
                ->indexBy('name')
                ->fetchAll();
            $dbValues = [];
            foreach ($configs as $config) {
                $dbValues[$config['name']] = $this->decode($config);
            }
            $values = array_merge($values, $dbValues);
        }

        return $values;
    }

    /**
     * @svc
     */
    protected function setAppMultiple($values, array $options = []): self
    {
        if (!$values) {
            return $this;
        }

        $configs = $this->createQuery(array_keys($values))->selectMain()->all();

        foreach ($values as $name => $value) {
            if (!isset($configs[$name])) {
                $configs[$name] = ConfigModel::fromArray(['name' => $name]);
            }

            [$type, $value] = $this->encode($value);
            $configs[$name]->fromArray([
                'type' => $type,
                'value' => $value,
            ]);
        }

        $configs->save();

        $data = [];
        foreach ($values as $key => $value) {
            $data[$this->cacheKeyPrefix . $key] = $value;
        }
        $this->cache->setMultiple($data);

        return $this;
    }

    /**
     * @svc
     */
    protected function getAppSection($name): array
    {
        // From cache ?

        // From database
        $configs = ConfigModel::select(['name', 'type', 'value'])
            ->where('name', 'like', $name . '.%')
            ->indexBy('name')
            ->fetchAll();

        $values = [];
        foreach ($configs as $config) {
            $values[substr($config['name'], strlen($name) + 1)] = $this->decode($config);
        }

        return $values;
    }

    protected function createQuery($names)
    {
        return ConfigModel::select(['name', 'type', 'value'])
            ->where('name', $names)
            ->indexBy('name');
    }

    protected function getCacheItem(string $name)
    {
        return $this->cache->get($this->cacheKeyPrefix . $name);
    }

    protected function setCacheItem(string $name, $value): self
    {
        $this->cache->set($this->cacheKeyPrefix . $name, $value);
        return $this;
    }

    protected function encode($value): array
    {
        $type = gettype($value);
        if (isset($this->nativeTypes[$type])) {
            return [
                $this->nativeTypes[$type],
                $value,
            ];
        }

        if ($type === 'array') {
            return [
                ConfigModel::TYPE_ARRAY,
                json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ];
        }

        if ($value instanceof \stdClass) {
            return [
                ConfigModel::TYPE_JSON,
                json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ];
        }

        return [
            ConfigModel::TYPE_OBJECT,
            serialize($value),
        ];
    }

    protected function decode(array $config)
    {
        $type = $config['type'];
        $value = $config['value'];
        switch ($type) {
            case ConfigModel::TYPE_STRING:
                return (string) $value;

            case ConfigModel::TYPE_INT:
                return (int) $value;

            case ConfigModel::TYPE_FLOAT:
                return (float) $value;

            case ConfigModel::TYPE_BOOL:
                return filter_var($value, \FILTER_VALIDATE_BOOLEAN);

            case ConfigModel::TYPE_ARRAY:
                return json_decode($value, true);

            case ConfigModel::TYPE_JSON:
                return json_decode($value);

            case ConfigModel::TYPE_OBJECT:
                return unserialize($value);

            case ConfigModel::TYPE_EXPRESS:
                $object = new stdClass();
                $object->express = (string) $value;

                return $object;

            default:
                return $value;
        }
    }

    /**
     * 设置一项配置的值
     *
     * @param array|string $name
     * @param mixed $value
     * @return $this
     */
    protected function update($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $item => $value) {
                $this->update($item, $value);
            }
            return $this;
        }

        if (isset($this->configs[$name])) {
            $this->configs[$name] = array_merge($this->configs[$name], $value);
        } else {
            $this->configs[$name] = $value;
        }
        return $this;
    }

    /**
     * @svc
     * @param array|string $name
     * @param mixed $value
     * @return void
     */
    protected function save($name, $value = null)
    {
        $this->update($name, $value)->write();
    }

    /**
     * @svc
     */
    protected function write()
    {
        $file = $this->getConfigFile();
        $content = $this->generateContent($this->configs);
        file_put_contents($file, $content);

        function_exists('opcache_invalidate') && opcache_invalidate($file);
    }

    /**
     * @svc
     */
    protected function load()
    {
        $this->env->loadConfigFile($this->getConfigFile());
        return $this;
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
}
