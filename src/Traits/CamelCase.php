<?php

namespace Miaoxing\Plugin\Traits;

use Wei\Logger;
use Wei\Wei;

/**
 * @property-read Wei $wei
 * @property-read Logger $logger
 */
trait CamelCase
{
    public static function bootCamelCase()
    {
        static::on('inputColumn', 'snake');

        static::on('outputColumn', 'camel');

        static::on('checkInputColumn', 'checkCamelCaseColumn');
    }

    protected function checkCamelCaseColumn($column)
    {
        // 填充的一般是用户传入的数据,避免使用两种格式造成混乱
        $pass = strpos($column, '_') === false;
        if (!$pass && $this->wei->has('logger')) {
            $this->logger->info('Ignore snake case column', ['column' => $column]);
        }

        return $pass;
    }
}
