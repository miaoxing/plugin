<?php

namespace Miaoxing\Plugin\Model;

trait DefaultScopeTrait
{
    /**
     * @var array
     */
    protected static $defaultScopes = [];

    /**
     * @var bool
     */
    protected $applyDefaultScope = false;

    /**
     * @var array|true
     */
    protected $withoutScopes = [];

    public static function bootDefaultScopeTrait()
    {
        static::on('preExecute', 'applyDefaultScope');
        static::on('preBuildQuery', 'applyDefaultScopePreBuildQuery');
    }

    /**
     * @param string $method
     * @return $this
     */
    public function addDefaultScope($method)
    {
        $class = static::class;

        static::$defaultScopes[$class][$method] = true;

        return $this;
    }

    public static function getDefaultScopes()
    {
        $class = static::class;

        return isset(static::$defaultScopes[$class]) ? static::$defaultScopes[$class] : [];
    }

    /**
     * @param array|string|true $scopes
     * @return $this
     * @svc
     */
    protected function unscoped($scopes = [])
    {
        if (!$scopes) {
            $this->withoutScopes = true;
            return $this;
        }

        if ($this->withoutScopes === true) {
            $this->withoutScopes = [];
        }
        $this->withoutScopes += (array) $scopes;
        return $this;
    }

    protected function applyDefaultScope()
    {
        if ($this->applyDefaultScope) {
            return;
        }
        $this->applyDefaultScope = true;

        if (true === $this->withoutScopes) {
            return;
        }

        if (!$defaultScopes = $this->getDefaultScopes()) {
            return;
        }

        $scopes = array_diff(array_keys($defaultScopes), $this->withoutScopes);
        foreach ($scopes as $scope) {
            $this->{$scope}();
        }

        return $this;
    }

    /**
     * @param string $sqlPartName
     */
    protected function applyDefaultScopePreBuildQuery($sqlPartName)
    {
        // 忽略初始化时设置table使用了from
        if ('from' !== $sqlPartName) {
            $this->applyDefaultScope();
        }
    }
}
