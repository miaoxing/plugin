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
        if (null !== $value && $this->hasCast($column) && !$this->isIgnoreCast($value)) {
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
     * Cast value to PHP type
     *
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function toPhpType($value, $type)
    {
        [$type, $options] = $this->parseType($type);

        switch ($type) {
            case 'int':
                return (int) $value;

            case 'string':
                return (string) $value;

            case 'datetime':
                return '0000-00-00 00:00:00' === $value ? '' : $value;

            case 'date':
                return '0000-00-00' === $value ? '' : $value;

            case 'bool':
                return (bool) $value;

            case 'array':
                return is_array($value) ? $value : (array) $this->cacheJsonDecode($value, true);

            case 'float':
                return (float) $value;

            case 'json':
                // Ignore default array value
                return is_array($value) ? $value : $this->cacheJsonDecode($value, true);

            case 'list':
                // Ignore default array value
                if (is_array($value)) {
                    return $value;
                }

                $value = explode($options['separator'] ?? ',', $value);
                if ($options['type'] ?? 'string' === 'int') {
                    $value = array_map('intval', $value);
                }

                return $value;

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
        [$type, $options] = $this->parseType($type);

        switch ($type) {
            case 'int':
                return (int) $value;

            case 'bool':
                return (bool) $value;

            case 'string':
            case 'float':
                return (string) $value;

            case 'date':
            case 'datetime':
                return $value ?: null;

            case 'array':
            case 'json':
                return json_encode((array) $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            case 'list':
                return implode($options['separator'] ?? ',', (array) $value);

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

    /**
     * @param string|array $type
     * @return array
     */
    private function parseType($type)
    {
        if (is_array($type)) {
            $options = $type;
            $type = $options[0];
            unset($options[0]);
        } else {
            $options = [];
        }
        return [$type, $options];
    }
}
