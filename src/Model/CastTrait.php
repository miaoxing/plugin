<?php

namespace Miaoxing\Plugin\Model;

use InvalidArgumentException;
use stdClass;

/**
 * @property-read array $casts
 * @property-read array $defaultCasts Helper for generating metadata
 */
trait CastTrait
{
    /**
     * @var array
     */
    protected static $castCache = [];

    protected static function bootCastTrait()
    {
        static::on('getValue', 'castToPhp');
        static::on('setValue', 'castToDb');
    }

    /**
     * Cast column value to PHP type
     *
     * @param mixed $value
     * @param string $column
     * @return mixed
     * @throws \Exception
     */
    protected function castToPhp($value, $column)
    {
        if ($value !== null && $this->hasCast($column) && !$this->isIgnoreCast($value)) {
            $value = $this->toPhpType($value, $this->casts[$column]);
        }

        return $value;
    }

    /**
     * Cast column value for saving to database
     *
     * @param mixed $value
     * @param string $column
     * @return mixed
     */
    protected function castToDb($value, $column)
    {
        if ($this->hasCast($column) && !$this->isIgnoreCast($value)) {
            $value = $this->toDbType($value, $this->casts[$column]);
        }

        return $value;
    }

    /**
     * Check if the specified column should be cast
     *
     * @param string $name
     * @return bool
     */
    public function hasCast($name)
    {
        return isset($this->casts) && isset($this->casts[$name]);
    }

    /**
     * Cast value to PHP type
     *
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function toPhpType($value, $type)
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

            case 'array':
            case 'json':
                // Ignore default array value
                return is_array($value) ? $value : $this->cacheJsonDecode($value, true);

            case 'float':
                return (float) $value;

            default:
                throw new InvalidArgumentException('Unsupported cast type: ' . $type);
        }
    }

    /**
     * Cast value for saving to database
     *
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    protected function toDbType($value, $type)
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

            case 'array':
                return json_encode((array) $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            default:
                throw new InvalidArgumentException('Unsupported cast type: ' . $type);
        }
    }

    /**
     * Cache json decode value
     *
     * @param string $value
     * @param bool $assoc
     * @return mixed
     */
    protected function cacheJsonDecode($value, $assoc = false)
    {
        if (!isset(static::$castCache[$value][$assoc])) {
            static::$castCache[$value][$assoc] = json_decode($value, $assoc);
        }

        return static::$castCache[$value][$assoc];
    }

    /**
     * @param mixed $value
     * @return bool
     * @todo object操作移到单独区域
     */
    protected function isIgnoreCast($value)
    {
        return $value instanceof stdClass && isset($value->scalar);
    }
}
