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
        $class = get_called_class();

        static::$defaultScopes[$class][$method] = true;

        return $this;
    }

    public function getDefaultScopes()
    {
        $class = get_called_class();

        return isset(static::$defaultScopes[$class]) ? static::$defaultScopes[$class] : [];
    }

    /**
     * @param string|array|true $scopes
     * @return $this
     * @api
     */
    protected function unscoped($scopes = [])
    {
        if (!$scopes) {
            $this->withoutScopes = true;
        } else {
            $this->withoutScopes += (array) $scopes;
        }

        return $this;
    }

    protected function applyDefaultScope()
    {
        if ($this->applyDefaultScope) {
            return;
        }
        $this->applyDefaultScope = true;

        if ($this->withoutScopes === true) {
            return;
        }

        if (!$defaultScopes = $this->getDefaultScopes()) {
            return;
        }

        // TODO confirm
        // 临时解决andWhere等中,被拦截时,已经设置了参数,还未设置语句,导致参数顺序错误
        //$temp = [$this->params, $this->paramTypes];
        //$this->params = $this->paramTypes = [];

        $scopes = array_diff(array_keys($defaultScopes), $this->withoutScopes);
        foreach ($scopes as $scope) {
            $this->$scope();
        }

        // 还原参数
        //$this->params = array_merge($this->params, $temp[0]);
        //$this->paramTypes = array_merge($this->paramTypes, $temp[1]);

        return $this;
    }

    /**
     * @param string $sqlPartName
     */
    protected function applyDefaultScopePreBuildQuery($sqlPartName)
    {
        // 忽略初始化时设置table使用了from
        if ($sqlPartName !== 'from') {
            $this->applyDefaultScope();
        }
    }
}
