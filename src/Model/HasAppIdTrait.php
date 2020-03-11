<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Service\App;

/**
 * @property-read string appIdColumn
 */
trait HasAppIdTrait
{
    public static function bootHasAppIdTrait(BaseModel $initModel)
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
        $appId = $app->getId();

        return $this->andWhere([$this->fullTable . '.' . $this->appIdColumn => $appId]);
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
