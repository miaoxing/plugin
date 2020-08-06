<?php

namespace Miaoxing\Plugin\Model;

use Wei\Logger;
use Wei\Req;
use Wei\Wei;

/**
 * @property-read Wei $wei
 * @property-read Logger $logger
 */
trait CamelCaseTrait
{
    public static function bootCamelCaseTrait()
    {
        static::on('checkInputColumn', 'checkCamelCaseColumn');
    }

    protected function checkCamelCaseColumn($column, $data)
    {
        // 对用户数据进行检查,避免使用两种格式造成混乱
        if ($data instanceof Req) {
            return false === strpos($column, '_');
        }

        return true;
    }
}
