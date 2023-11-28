<?php

namespace Miaoxing\Plugin\Model;

/**
 * Add user IP to model object
 *
 * @property-read string $createdIpColumn The column contains create user IP
 * @mixin \ReqPropMixin
 */
trait IpTrait
{
    public static function bootIpTrait(): void
    {
        static::onModelEvent('init', 'initIpModel');
        static::onModelEvent('beforeCreate', 'setIpValue');
    }

    protected function getCreatedIpColumn(): string
    {
        return $this->createdIpColumn ?? $this->convertToPhpKey('created_ip');
    }

    /**
     * @internal
     */
    protected function initIpModel(): void
    {
        $createdIpColumn = $this->getCreatedIpColumn();
        $this->hidden[] = $createdIpColumn;
        $this->guarded[] = $createdIpColumn;
        $this->columns[$createdIpColumn]['cast'] = 'ip';
    }

    /**
     * @internal
     */
    protected function setIpValue(): void
    {
        $this->setColumnValue($this->getCreatedIpColumn(), $this->req->getIp());
    }
}
