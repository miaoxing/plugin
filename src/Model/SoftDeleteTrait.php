<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Service\Model;
use Miaoxing\Plugin\Service\User;
use Miaoxing\Services\Service\Time;

/**
 * @property-read string deletedAtColumn
 * @property-read string deletedByColumn
 */
trait SoftDeleteTrait
{
    /**
     * @var bool
     */
    protected $reallyDestroy = false;

    /**
     * @param Model $initModel
     */
    public static function bootSoftDeleteTrait(Model $initModel)
    {
        $initModel->addDefaultScope('withoutDeleted');
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return (bool) $this->get($this->deletedAtColumn);
    }

    /**
     * @return $this
     */
    public function restore()
    {
        return $this->saveData([
            $this->deletedAtColumn => '',
            $this->deletedByColumn => 0,
        ]);
    }

    /**
     * @param mixed $conditions
     * @return $this
     * @api
     */
    protected function reallyDestroy($conditions = false)
    {
        $this->reallyDestroy = true;
        $this->destroy($conditions);
        $this->reallyDestroy = false;

        return $this;
    }

    /**
     * @return $this
     * @api
     */
    protected function withoutDeleted()
    {
        return $this->whereNull($this->deletedAtColumn);
    }

    /**
     * @return $this
     * @api
     */
    protected function onlyDeleted()
    {
        return $this->unscoped('withoutDeleted')->whereNotNull($this->deletedAtColumn);
    }

    /**
     * @return $this
     * @api
     */
    protected function withDeleted()
    {
        return $this->unscoped('withoutDeleted');
    }

    /**
     * Overwrite original destroy logic.
     */
    protected function executeDestroy()
    {
        if ($this->reallyDestroy) {
            parent::executeDestroy();
        } else {
            $this->saveData([
                $this->deletedAtColumn => Time::now(),
                $this->deletedByColumn => User::id() ?: 0,
            ]);
        }
    }
}
