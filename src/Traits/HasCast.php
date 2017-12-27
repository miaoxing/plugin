<?php

namespace Miaoxing\Plugin\Traits;

/**
 * @property-read array $casts
 */
trait HasCast
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected static function bootHasCast()
    {
        static::on('getValue', 'castValue');
    }

    /**
     * @param mixed $value
     * @param string $column
     * @return mixed
     * @throws \Exception
     */
    protected function castValue($value, $column)
    {
        if ($value !== null && isset($this->casts) && isset($this->casts[$column])) {
            $value = $this->cast($this->casts[$column], $value);
        }

        return $value;
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return mixed
     * @throws \Exception
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
                throw new \Exception('Unsupported cast type: ' . $type);
        }
    }
}
