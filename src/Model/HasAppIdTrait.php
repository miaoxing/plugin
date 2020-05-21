<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Service\App;
use Miaoxing\Plugin\Service\Model;

/**
 * @property-read string appIdColumn
 */
trait HasAppIdTrait
{
    public static function bootHasAppIdTrait(Model $initModel)
    {
        $initModel->addDefaultScope('curApp');

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

        return $this->where($this->getTable() . '.' . $this->appIdColumn, $app->getId());
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
        return $this->set($this->appIdColumn, $appId ?: $app->getId());
    }
}
