<?php

namespace Miaoxing\Plugin\Service;

use Miaoxing\Plugin\BaseService;

/**
 * @mixin \EnvMixin
 */
class Config extends BaseService
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

    public function __construct($options)
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
     * 设置一项配置的值
     *
     * @param array|string $name
     * @param mixed $value
     * @return $this
     */
    protected function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $item => $value) {
                $this->set($item, $value);
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
        $this->set($name, $value)->write();
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
