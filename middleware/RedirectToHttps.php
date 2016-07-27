<?php

namespace miaoxing\plugin\middleware;

class RedirectToHttps extends Base
{
    /**
     * 要跳转到HTTPS地址的域名,如example.com
     *
     * @var array
     */
    protected $hosts = [];

    /**
     * {@inheritdoc}
     */
    public function __invoke($next)
    {
        if (!$this->hosts) {
            return $next();
        }

        if ($this->request->getScheme() == 'http' && in_array($this->request->getHost(), $this->hosts)) {
            $url = 'https' . substr($this->request->getUrl(), 4);
            return $this->response->redirect($url);
        }

        return $next();
    }
}
