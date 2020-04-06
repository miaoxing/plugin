<?php

namespace Miaoxing\Plugin\Db;

use Wei\Base;
use Wei\Db;

/**
 * @property Db db
 */
abstract class BaseDriver extends Base
{
    protected $wrapper = '';

    /**
     * The table name alias used in the query
     *
     * @var array
     */
    protected $aliases = [];

    protected function wrap($column)
    {
        if (strpos($column, '.') === false) {
            return $this->wrapValue($column);
        }

        $items = explode('.', $column);

        // 倒数第二项是数据表名称，例如：db.table.column
        $tableIndex = count($items) - 2;

        foreach ($items as $i => &$item) {
            if ($i === $tableIndex) {
                $item = $this->wrapTable($item);
            } else {
                $item = $this->wrapValue($item);
            }
        }

        return implode('.', $items);
    }

    protected function wrapTable($table)
    {
        return $this->wrap($this->isAlias($table) ? $table : $this->db->getTable($table));
    }

    protected function addAlias($name)
    {
        $this->aliases[$name] = true;
        return $this;
    }

    protected function isAlias($name)
    {
        return isset($this->aliases[$name]);
    }

    protected function wrapValue(string $value): string
    {
        return $value === '*' ? $value : $this->wrapper . $value . $this->wrapper;
    }

    protected function getRawValue($expression)
    {
        return $expression->scalar;
    }

    protected function isRaw($expression)
    {
        return $expression instanceof \stdClass && isset($expression->scalar);
    }
}
