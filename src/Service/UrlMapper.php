<?php

namespace Miaoxing\Plugin\Service;

use miaoxing\plugin\BaseService;

/**
 * URL映射
 *
 * @property \Wei\Request $request
 * @property \Wei\StatsD $statsD
 */
class UrlMapper extends BaseService
{
    /**
     * 映射数组
     *
     * @var array
     */
    protected $map = [];

    public function __invoke()
    {
        $req = $this->request;
        $path = ltrim($req->getPathInfo(), '/');

        // 替换整个请求路径
        if (isset($this->map[$path])) {
            $this->setPathInfo('/' . $this->map[$path]);

            return;
        }

        // 替换控制器
        $pos = strrpos($path, '/');
        if ($pos !== false) {
            $controller = substr($path, 0, $pos);
            if (isset($this->map[$controller])) {
                $this->setPathInfo('/' . $this->map[$controller] . substr($path, $pos));
            }
        }
    }

    public function matchMap($inputUrl, $outputUrl)
    {
        foreach ($this->map as $key => $value) {
            if ($this->request->getBaseUrl() . '/' . $key == $inputUrl
                && $this->request->getBaseUrl() . '/' . $value == $outputUrl
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * 更改路径并上报旧路径
     *
     * @param string $path
     */
    protected function setPathInfo($path)
    {
        $key = 'urlMapper.' . ltrim($this->request->getBaseUrl(), '/') . $this->request->getPathInfo();
        $this->request->setPathInfo($path);
        $this->statsD->increment($key);
    }
}
