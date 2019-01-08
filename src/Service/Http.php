<?php

namespace Miaoxing\Plugin\Service;

use Wei\RetTrait;

class Http extends \Wei\Http
{
    use RetTrait;

    /**
     * 返回 ret 格式的 HTTP 请求结果
     *
     * @param array $data
     * @return array
     */
    public function toRet(array $data = [])
    {
        if ($this->isSuccess()) {
            return $this->suc($data + (array) $this->getResponse());
        }

        $e = $this->getErrorException();
        return $this->err($data + ['code' => $e->getCode(), 'message' => $e->getMessage()]);
    }
}
