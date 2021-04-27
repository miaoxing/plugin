<?php

namespace Miaoxing\Plugin\Model;

use InvalidArgumentException;
use stdClass;

/**
 * Add cast functions to the model
 *
 * @internal Expected to be used only by ModelTrait
 */
trait CastTrait
{
    /**
     * @var array
     */
    protected static $castCache = [];

    /**
     * Get the column cast configs
     *
     * @return array
     */
    public function getColumnCasts(): array
    {
        return $this->getColumnValues('cast');
    }

    /**
     * Get the cast config for specified column
     *
     * @param string $column
     * @return string|array|null
     */
    public function getColumnCast(string $column)
    {
        return $this->getColumns()[$column]['cast'] ?? null;
    }

    /**
     * Check if the specified column should be cast
     *
     * @param string $name
     * @return bool
     */
    public function hasColumnCast(string $name): bool
    {
        return (bool) $this->getColumnCast($name);
    }

    /**
     * Cast column value to PHP type
     *
     * @param mixed $value
     * @param string $column
     * @return mixed
     * @throws \Exception
     */
    protected function castColumnToPhp($value, string $column)
    {
        if (null === $value && ($this->getColumns()[$column]['nullable'] ?? false)) {
            return null;
        }

        if (!$this->isIgnoreCast($value) && $cast = $this->getColumnCast($column)) {
            return $this->castValueToPhp($value, $cast);
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
    protected function castColumnToDb($value, string $column)
    {
        if (null === $value && ($this->getColumns()[$column]['nullable'] ?? false)) {
            return null;
        }

        if (!$this->isIgnoreCast($value) && $cast = $this->getColumnCast($column)) {
            return $this->castValueToDb($value, $cast);
        }

        return $value;
    }

    /**
     * Cast value to PHP type
     *
     * @param mixed $value
     * @param string|array $type
     * @return mixed
     */
    protected function castValueToPhp($value, $type)
    {
        [$type, $options] = $this->parseCastType($type);

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

            case 'object':
                return is_object($value) ? $value : json_decode($value);

            case 'list':
                // Ignore default array value
                if (is_array($value)) {
                    return $value;
                }

                if (in_array($value, [null, ''], true)) {
                    return [];
                }

                $value = explode($options['separator'] ?? ',', $value);
                if (($options['type'] ?? 'string') === 'int') {
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
     * @param string|array $type
     * @param mixed $value
     * @return mixed
     */
    protected function castValueToDb($value, $type)
    {
        [$type, $options] = $this->parseCastType($type);

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
                // Note that default value of the date* column is null, so they are always nullable
                return $value ?: null;

            case 'array':
            case 'json':
                return json_encode((array) $value, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

            case 'object':
                // TODO keep original object, and cast to db string before save
                return json_encode((object) $value, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

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
     * @return array|stdClass
     */
    protected function cacheJsonDecode(string $value, bool $assoc = false)
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
    protected function isIgnoreCast($value): bool
    {
        return $value instanceof stdClass && isset($value->scalar);
    }

    /**
     * @param string|array $type
     * @return array
     */
    private function parseCastType($type): array
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
