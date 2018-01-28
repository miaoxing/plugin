<?php

namespace Miaoxing\Plugin\Model;

use Wei\Logger;
use Wei\Wei;

/**
 * @property-read Wei $wei
 * @property-read Logger $logger
 */
trait CamelCaseTrait
{
    public static function bootCamelCaseTrait()
    {
        static::on('inputColumn', 'snake');

        static::on('outputColumn', 'camel');

        static::on('checkInputColumn', 'checkCamelCaseColumn');
    }

    protected function checkCamelCaseColumn($column)
    {
        // 填充的一般是用户传入的数据,避免使用两种格式造成混乱
        return false === strpos($column, '_');
    }
}
