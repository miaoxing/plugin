<?php

namespace Miaoxing\Plugin\Traits;

use InvalidArgumentException;

/**
 * @property-read array $casts
 */
trait HasCast
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected static function bootHasCast()
    {
        static::on('getValue', 'castValue');
        static::on('setValue', 'setValue');
    }

    /**
     * @param mixed $value
     * @param string $column
     * @return mixed
     * @throws \Exception
     */
    protected function castValue($value, $column)
    {
        if ($value !== null && $this->hasCast($column)) {
            $value = $this->cast($this->casts[$column], $value);
        }

        return $value;
    }

    protected function setValue($value, $column)
    {
        if ($this->hasCast($column)) {
            $value = $this->castToDb($this->casts[$column], $value);
        }

        return $value;
    }

    protected function hasCast($name)
    {
        return isset($this->casts) && isset($this->casts[$name]);
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function cast($type, $value)
    {
        switch ($type) {
            case 'int':
                return (int) $value;

            case 'string':
                return (string) $value;

            case 'datetime':
                return $value === '0000-00-00 00:00:00' ? '' : $value;

            case 'date':
                return $value === '0000-00-00' ? '' : $value;

            case 'bool':
                return (bool) $value;

            case 'json':
                return json_decode($value, true);

            case 'float':
                return (float) $value;

            default:
                throw new InvalidArgumentException('Unsupported cast type: ' . $type);
        }
    }

    protected function castToDb($type, $value)
    {
        switch ($type) {
            case 'int':
            case 'bool':
                return (int) $value;

            case 'string':
            case 'datetime': // Coverts by database
            case 'date':
            case 'float':
                return $value;

            case 'json':
                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            default:
                throw new InvalidArgumentException('Unsupported cast type: ' . $type);
        }
    }
}
