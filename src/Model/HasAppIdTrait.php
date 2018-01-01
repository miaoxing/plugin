<?php

namespace Miaoxing\Plugin\Model;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Service\App;
use Wei\Record;

/**
 * @property-read App app
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
        $appId = $this->app->getId();

        return $this->andWhere([$this->appIdColumn => $appId]);
    }

    /**
     * Record: Set value for app id column
     *
     * @param int|null $appId
     * @return $this
     */
    public function setAppId($appId = null)
    {
        return $this->set($this->appIdColumn, $appId ?: wei()->app->getId());
    }
}
