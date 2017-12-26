<?php

namespace Miaoxing\Plugin\Traits;

trait HasCast
{
    protected $casts = [];

    protected $dateFormat = 'Y-m-d H:i:s';

    protected function bootHasCast()
    {
        static::on('getValue', 'castColumn');
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return mixed
     * @throws \Exception
     */
    protected function castColumn($column, $value)
    {
        if ($value !== null && isset($this->casts[$column])) {
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
