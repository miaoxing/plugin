<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Service\User;
use Miaoxing\Plugin\Service\WeiBaseModel;
use Wei\Time;

/**
 * Add soft delete function to model class
 *
 * @property-read string $deletedAtColumn The column contains delete time
 * @property-read string $deletedByColumn The column contains delete user id
 * @property-read string $deleteStatusColumn The column contains delete status value
 */
trait SoftDeleteTrait
{
    /**
     * Indicates whether really remove the record from database
     *
     * @var bool
     */
    protected $reallyDestroy = false;

    /**
     * Bootstrap the trait
     *
     * @param BaseModel $initModel
     */
    public static function bootSoftDeleteTrait(WeiBaseModel $initModel)
    {
        $initModel->addDefaultScope('withoutDeleted');
        static::on('init', 'addedDeleteColumnToGuarded');
        static::on('destroy', 'executeSoftDelete');
    }

    /**
     * Indicate whether the model has been soft deleted
     *
     * @return bool
     */
    public function isDeleted()
    {
        return (bool) $this->get($this->getDeletedAtColumn());
    }

    /**
     * Restore the record to the normal state
     *
     * @return $this
     */
    public function restore()
    {
        $data = [
            $this->getDeletedAtColumn() => null,
            $this->getDeletedByColumn() => 0,
        ];
        if ($statusColumn = $this->getDeleteStatusColumn()) {
            $data[$statusColumn] = $this->getRestoreStatusValue();
        }
        return $this->saveData($data);
    }

    /**
     * Really remove the record from database
     *
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
     * Add a query to filter soft deleted records
     *
     * @return $this
     * @svc
     */
    protected function withoutDeleted()
    {
        if ($statusColumn = $this->getDeleteStatusColumn()) {
            return $this->where($statusColumn, '!=', $this->getDeleteStatusValue());
        } else {
            return $this->whereNull($this->getDeletedAtColumn());
        }
    }

    /**
     * Add a query to return only deleted records
     *
     * @return $this
     * @svc
     */
    protected function onlyDeleted()
    {
        $this->unscoped('withoutDeleted');
        if ($statusColumn = $this->getDeleteStatusColumn()) {
            return $this->where($statusColumn, $this->getDeleteStatusValue());
        } else {
            return $this->whereNotNull($this->getDeletedAtColumn());
        }
    }

    /**
     * Remove "withoutDeleted" in the query, expect to return all records
     *
     * @return $this
     * @svc
     */
    protected function withDeleted()
    {
        return $this->unscoped('withoutDeleted');
    }

    protected function getDeletedAtColumn()
    {
        return $this->deletedAtColumn ?? 'deleted_at';
    }

    protected function getDeletedByColumn()
    {
        return $this->deletedByColumn ?? 'deleted_by';
    }

    /**
     * Get the column of delete status
     *
     * The model class can override this method to customize the value of the delete state
     *
     * @return string|null
     */
    protected function getDeleteStatusColumn()
    {
        return property_exists($this, 'deleteStatusColumn') ? $this->deleteStatusColumn : null;
    }

    /**
     * Get the value of delete status
     *
     * The model class can override this method to customize the value of the delete state
     *
     * @return int
     */
    protected function getDeleteStatusValue()
    {
        return 1;
    }

    /**
     * Get the value of restore status
     *
     * The model class can override this method to customize the value of the restore state
     *
     * @return int
     */
    protected function getRestoreStatusValue()
    {
        return 0;
    }

    /**
     * @internal
     */
    protected function executeSoftDelete()
    {
        if ($this->reallyDestroy) {
            return false;
        }

        $data = [
            $this->getDeletedAtColumn() => Time::now(),
            $this->getDeletedByColumn() => User::id() ?: 0,
        ];
        if ($statusColumn = $this->getDeleteStatusColumn()) {
            $data[$statusColumn] = $this->getDeleteStatusValue();
        }
        $this->saveData($data);
        return true;
    }

    /**
     * @internal
     */
    protected function addedDeleteColumnToGuarded()
    {
        $this->guarded = array_merge($this->guarded, array_filter([
            $this->getDeletedAtColumn(),
            $this->getDeletedByColumn(),
            $this->getDeleteStatusColumn(),
        ]));
    }
}
