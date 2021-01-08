<?php

namespace Miaoxing\Plugin\Model;

trait CamelCaseTrait
{
    public static function bootCamelCaseTrait(): void
    {
        static::on('init', 'setCamelCaseKeyConverter');
    }

    public function setCamelCaseKeyConverter(): void
    {
        $this->setDbKeyConverter([$this, 'snake']);
        $this->setPhpKeyConverter([$this, 'camel']);
    }
}
