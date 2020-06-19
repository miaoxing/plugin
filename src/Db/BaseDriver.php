<?php

namespace Miaoxing\Plugin\Db;

use Wei\Base;

/**
 * @mixin \DbMixin
 */
abstract class BaseDriver extends Base
{
    /**
     * @var string
     */
    protected $wrapper = '';

    /**
     * @var callable|null
     */
    protected $identifierConverter;

    /**
     * The table name alias used in the query
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * @param string $type
     * @param array $sqlParts
     * @param callable|null $identifierConverter
     * @return string
     */
    abstract function getSql($type, $sqlParts, $identifierConverter = null);

    /**
     * @param string $type
     * @param array $sqlParts
     * @param callable|null $identifierConverter
     * @param array $values
     * @return string
     */
    abstract function getRawSql($type, $sqlParts, $identifierConverter, array $values);

    protected function wrap($column)
    {
        if (false === strpos($column, '.')) {
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
        if ('*' === $value) {
            return $value;
        }

        if ($this->identifierConverter) {
            $value = call_user_func($this->identifierConverter, $value);
        }

        return $this->wrapper . $value . $this->wrapper;
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
