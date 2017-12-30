<?php

namespace Miaoxing\Plugin\Traits;

use InvalidArgumentException;

/**
 * @property-read array $casts
 */
trait HasCast
{
    /**
     * @var array
     */
    protected static $castCache = [];

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
            $value = $this->toPhpType($this->casts[$column], $value);
        }

        return $value;
    }

    protected function setValue($value, $column)
    {
        if ($this->hasCast($column)) {
            $value = $this->toDbType($this->casts[$column], $value);
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
    protected function toPhpType($type, $value)
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
                return $this->cacheJsonDecode($value, true);

            case 'float':
                return (float) $value;

            default:
                throw new InvalidArgumentException('Unsupported cast type: ' . $type);
        }
    }

    protected function toDbType($type, $value)
    {
        switch ($type) {
            case 'int':
                return (int) $value;

            case 'bool':
                return (bool) $value;

            case 'string':
            case 'datetime': // Coverts by database
            case 'date':
            case 'float':
                return (string) $value;

            case 'json':
                // Ignore initial string
                return is_string($value) ? $value : json_encode($value,
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            default:
                throw new InvalidArgumentException('Unsupported cast type: ' . $type);
        }
    }

    protected function cacheJsonDecode($value, $assoc = false)
    {
        if (!isset(static::$castCache[$value][$assoc])) {
            static::$castCache[$value][$assoc] = json_decode($value, $assoc);
        }

        return static::$castCache[$value][$assoc];
    }
}
