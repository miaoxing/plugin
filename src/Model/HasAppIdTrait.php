<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\BaseModel;
use Miaoxing\Plugin\Service\App;

trait HasAppIdTrait
{
    public static function bootHasAppIdTrait(BaseModel $initModel): void
    {
        $initModel->addDefaultScope('curApp');

        static::onModelEvent('init', 'addAppIdToGuarded');
        static::onModelEvent('beforeCreate', 'setAppId');
    }

    /**
     * Query: Filter by current app id
     *
     * @return $this
     */
    public function curApp(): self
    {
        /** @var App $app */
        $app = $this->wei->app;
        return $this->where('appId', $app->getId());
    }

    /**
     * Record: Set value for app id column
     *
     * @param int|null $appId
     * @return $this
     */
    public function setAppId($appId = null): self
    {
        /** @var App $app */
        $app = $this->wei->app;
        return $this->set('appId', $appId ?: $app->getId());
    }

    /**
     * @internal
     */
    protected function addAppIdToGuarded(): void
    {
        array_push($this->guarded, 'appId');
    }
}
