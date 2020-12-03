<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Service\App;
use Miaoxing\Plugin\Service\Model;

trait HasAppIdTrait
{
    public static function bootHasAppIdTrait(Model $initModel)
    {
        $initModel->addDefaultScope('curApp');

        static::on('init', 'addAppIdToGuarded');
        static::on('beforeCreate', 'setAppId');
    }

    /**
     * Query: Filter by current app id
     *
     * @return $this
     */
    public function curApp()
    {
        /** @var App $app */
        $app = $this->wei->app;
        return $this->where('app_id', $app->getId());
    }

    /**
     * Record: Set value for app id column
     *
     * @param int|null $appId
     * @return $this
     */
    public function setAppId($appId = null)
    {
        /** @var App $app */
        $app = $this->wei->app;
        return $this->set('appId', $appId ?: $app->getId());
    }

    /**
     * @internal
     */
    protected function addAppIdToGuarded()
    {
        array_push($this->guarded, 'appId');
    }
}
