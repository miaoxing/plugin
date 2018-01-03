<?php

namespace Miaoxing\Plugin\Service;

/**
 * @property \Wei\BaseCache $cache
 */
class Db extends \Wei\Db
{
    /**
     * {@inheritdoc}
     */
    public function getTableFields($table, $withPrefix = false)
    {
        $fullTable = $withPrefix ? $table : $this->getTable($table);
        if (isset($this->tableFields[$fullTable])) {
            return $this->tableFields[$fullTable];
        } else {
            return (array)$this->cache->get('tableFields' . $this->dbname . '.' . $table, 60, function () use ($table, $withPrefix) {
                return parent::getTableFields($table, $withPrefix);
            });
        }
    }
}
