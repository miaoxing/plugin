<?php

namespace Miaoxing\Plugin\Model;

use Miaoxing\Plugin\Service\Ret;

trait ModelTrait
{
    use WeiModelTrait;

    /**
     * 返回当前对象
     *
     * @return $this
     * @deprecated 改用 $wei->get($modelName) 返回模型实例
     * @internal 用于兼容已有逻辑，待删除
     */
    public function __invoke(): self
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     * @svc
     */
    protected function toRet(array $merge = []): Ret
    {
        return parent::toRet($merge);
    }
}
