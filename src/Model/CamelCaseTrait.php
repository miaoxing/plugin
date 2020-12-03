<?php

namespace Miaoxing\Plugin\Model;

trait CamelCaseTrait
{
    public static function bootCamelCaseTrait()
    {
        static::on('init', 'setCamelCaseIdentifierConverter');
    }

    public function setCamelCaseIdentifierConverter()
    {
        $this->setInputIdentifierConverter([$this, 'snake']);
        $this->setOutputIdentifierConverter([$this, 'camel']);
    }
}
