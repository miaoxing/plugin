<?php

namespace Miaoxing\Plugin\Model;

trait DefaultScopeTrait
{
    protected static $defaultScopes = [];

    /**
     * @var array|true
     */
    protected $withoutScopes = [];

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
     */
    public function unscoped($scopes = [])
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
        if ($this->withoutScopes === true) {
            return;
        }

        if (!$defaultScopes = $this->getDefaultScopes()) {
            return;
        }

        // 临时解决andWhere等中,被拦截时,已经设置了参数,还未设置语句,导致参数顺序错误
        $temp = [$this->params, $this->paramTypes];
        $this->params = $this->paramTypes = [];

        $scopes = array_diff(array_keys($defaultScopes), $this->withoutScopes);
        foreach ($scopes as $scope) {
            $this->$scope();
        }

        // 还原参数
        $this->params = array_merge($this->params, $temp[0]);
        $this->paramTypes = array_merge($this->paramTypes, $temp[1]);

        return $this;
    }
}
