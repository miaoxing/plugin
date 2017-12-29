<?php

namespace Miaoxing\Plugin\Traits;

trait SoftDeletes
{
    protected $reallyDestroy = false;

    public static function bootSoftDeletes()
    {
        static::addDefaultScope('notDeleted');
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isDeleted()
    {
        $value = $this->get($this->deletedAtColumn);

        return $value && $value !== '0000-00-00 00:00:00';
    }

    public function restore()
    {
        return $this->saveData([$this->deletedAtColumn => '']);
    }

    public function reallyDestroy($conditions = false)
    {
        $this->reallyDestroy = true;
        $this->destroy($conditions);
        $this->reallyDestroy = false;

        return $this;
    }

    public function withoutDeleted()
    {
        return $this->andWhere([$this->fullTable . '.' . $this->deletedAtColumn => '0000-00-00 00:00:00']);
    }

    public function onlyDeleted()
    {
        return $this->unscoped('notDeleted')
            ->andWhere($this->deletedAtColumn . " != '0000-00-00 00:00:00'");
    }

    public function withDeleted()
    {
        return $this->unscoped('notDeleted');
    }

    protected function executeDestroy()
    {
        if ($this->reallyDestroy) {
            parent::executeDestroy();
        } else {
            $this->saveData([
                $this->deletedAtColumn => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
