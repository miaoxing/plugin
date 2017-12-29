<?php

namespace Miaoxing\Plugin\Traits;

use miaoxing\plugin\BaseModel;

trait SoftDelete
{
    /**
     * @var bool
     */
    protected $reallyDestroy = false;

    /**
     * @param BaseModel $initModel
     */
    public static function bootSoftDelete(BaseModel $initModel)
    {
        $initModel->addDefaultScope('withoutDeleted');
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

    /**
     * @return $this
     */
    public function restore()
    {
        return $this->saveData([$this->deletedAtColumn => '']);
    }

    /**
     * @param mixed $conditions
     * @return $this
     */
    public function reallyDestroy($conditions = false)
    {
        $this->reallyDestroy = true;
        $this->destroy($conditions);
        $this->reallyDestroy = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function withoutDeleted()
    {
        return $this->andWhere([$this->fullTable . '.' . $this->deletedAtColumn => '0000-00-00 00:00:00']);
    }

    /**
     * @return $this
     */
    public function onlyDeleted()
    {
        return $this->unscoped('withoutDeleted')
            ->andWhere($this->deletedAtColumn . " != '0000-00-00 00:00:00'");
    }

    /**
     * @return $this
     */
    public function withDeleted()
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
                $this->deletedAtColumn => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
