<?php

namespace Miaoxing\Plugin\Traits;

trait SoftDeletes
{
    public static function bootSoftDeletes()
    {
        static::addDefaultScope('notDeleted');
    }

    protected function executeDestroy()
    {
        $this->saveData([
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function onlyDeleted()
    {
        return $this->unscoped('notDeleted')
            ->andWhere("deleted_at != '0000-00-00 00:00:00'");
    }

    public function withDeleted()
    {
        return $this->unscoped('notDeleted');
    }

    public function isDeleted()
    {

    }
}
