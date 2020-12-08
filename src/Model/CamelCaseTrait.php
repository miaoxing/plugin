<?php

namespace Miaoxing\Plugin\Model;

trait CamelCaseTrait
{
    public static function bootCamelCaseTrait()
    {
        static::on('init', 'setCamelCaseKeyConverter');
    }

    public function setCamelCaseKeyConverter()
    {
        $this->setDbKeyConverter([$this, 'snake']);
        $this->setPhpKeyConverter([$this, 'camel']);
    }
}
