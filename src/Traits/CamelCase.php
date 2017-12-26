<?php

namespace Miaoxing\Plugin\Traits;

trait CamelCase
{
    public function bootCamelCase()
    {
        static::on('inputColumn', ['static', 'snake']);

        static::on('outputColumn', ['static', 'camel']);

        static::on('checkInputColumn', function ($column) {
            // 填充的一般是用户传入的数据,避免使用两种格式造成混乱
            return strpos($column, '_') === false;
        });
    }
}
