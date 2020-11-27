<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Service\Model;
use Miaoxing\Plugin\Service\User;
use Wei\Time;

/**
 * @property-read string $deletedAtColumn The column contains delete time
 * @property-read string $deletedByColumn The column contains delete user id
 * @property-read string $deleteStatusColumn The column contains delete status value
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
        $data = [
            $this->deletedAtColumn => null,
            $this->deletedByColumn => 0,
        ];
        if ($statusColumn = $this->getDeleteStatusColumn()) {
            $data[$statusColumn] = $this->getRestoreStatusValue();
        }
        return $this->saveData($data);
    }

    /**
     * @param mixed $conditions
     * @return $this
     * @svc
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
     * @svc
     */
    protected function withoutDeleted()
    {
        if ($statusColumn = $this->getDeleteStatusColumn()) {
            return $this->where($statusColumn, '!=', $this->getDeleteStatusValue());
        } else {
            return $this->whereNull($this->deletedAtColumn);
        }
    }

    /**
     * @return $this
     * @svc
     */
    protected function onlyDeleted()
    {
        $this->unscoped('withoutDeleted');
        if ($statusColumn = $this->getDeleteStatusColumn()) {
            return $this->where($statusColumn, $this->getDeleteStatusValue());
        } else {
            return $this->whereNotNull($this->deletedAtColumn);
        }
    }

    /**
     * @return $this
     * @svc
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
            $data = [
                $this->deletedAtColumn => Time::now(),
                $this->deletedByColumn => User::id() ?: 0,
            ];
            if ($statusColumn = $this->getDeleteStatusColumn()) {
                $data[$statusColumn] = $this->getDeleteStatusValue();
            }
            $this->saveData($data);
        }
    }

    protected function getDeleteStatusColumn()
    {
        return property_exists($this, 'deleteStatusColumn') ? $this->deleteStatusColumn : null;
    }

    protected function getDeleteStatusValue()
    {
        return 1;
    }

    protected function getRestoreStatusValue()
    {
        return 0;
    }
}
