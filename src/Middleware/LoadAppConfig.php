<?php

namespace Miaoxing\Plugin\Middleware;

/**
 * 如果请求地址包含其他应用的ID,加载该应用的配置
 */
class LoadAppConfig extends \Miaoxing\Plugin\Middleware\Base
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        $fromAppId = $this->request['fromAppId'];
        if ($fromAppId) {
            $fromApp = wei()->appRecord()->findOneById($fromAppId);
            $this->wei->env->loadConfigDir('plugins/' . $fromApp['name'] . '/configs');
        }

        return $next();
    }
}
