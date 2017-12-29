<?php

namespace Miaoxing\Plugin\Traits;

use miaoxing\plugin\BaseModel;
use Miaoxing\Plugin\Service\App;
use Wei\Record;

/**
 * @property-read App app
 * @property-read array $data
 * @see Record::$data
 * @property-read string appIdColumn
 */
trait HasAppId
{
    public static function bootHasAppId(BaseModel $initModel)
    {
        $initModel->addDefaultScope('curApp');
    }

    /**
     * @return $this
     */
    public function curApp()
    {
        $appId = $this->app->getId();

        return $this->andWhere([$this->appIdColumn => $appId]);
    }

    /**
     * Overwrite
     *
     * @todo 看怎么改为事件
     */
    public function beforeCreate()
    {
        parent::beforeCreate();

        $this[$this->appIdColumn] = $this->app->getId();
    }
}
